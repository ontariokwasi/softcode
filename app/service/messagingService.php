<?php
require_once __DIR__ . '/../client/messagingClient.php';
require_once __DIR__ . '/../service/authService.php';
require_once __DIR__ . '/../constants.php';
require_once __DIR__ . '/../dao/messagingDao.php';
require_once __DIR__ . '/../model/message/message.php';
require_once __DIR__ . '/../common/logger.php';

interface MessagingService
{
    public function send(string $sender, string $message, string $destination, string $opId): string;
    public function getActiveContents(string $serviceId): array;
    public function getLatestRefIds(string $msisdn): array;
    public function saveToBench(string $msisdn, string $serviceId, string $offerName);
    public function getbenchedMsisdns(string $serviceId, string $offerName): array;
}

class VodafoneMessagingService implements MessagingService
{
    private AuthService $authService;
    private MessagingClient $client;
    private MessagingDao $dao;
    private Logger $logger;
    public function __construct()
    {
        $this->authService = new VodafoneAuthService();
        $this->client = new VodafoneMessagingCleint();
        $this->dao = new MessagingDao();
        $this->logger = Logger::getDefaultInstanceWithId($this);
    }
    public function send(string $sender, string $message, string $destination, string $opId, string $contentRef = '0'): string
    {
        $this->logger->debug("getting token..");
        $token = $this->authService->getAccessToken($opId);
        $this->logger->debug("token received: " . $token);
        if ($token) {
            $resp =  $this->client->send($sender, $message, $destination, uniqid(), $token);
            $respArr = json_decode($resp, true);
            $status = $respArr['outboundSMSMessageRequest']['deliveryInfoList']['deliveryInfo'][0]['deliveryStatus'];
            $mesg = new Message($sender, $destination, $message, $status);
            $this->dao->saveMessage($mesg, $contentRef);
            return $resp;
        }
        return "retrieving access token for $opId failed!";
    }
    public function sendBulk(string $sender, string $message, array $destinations, string $opId): void
    {
        $this->logger->debug("getting token for bulk..");
        $token = $this->authService->getAccessToken($opId);
        if ($token) {
            foreach ($destinations as $destination) {
                try {
                    $resp =  $this->client->send($sender, $message, $destination, uniqid(), $token);
                    $respArr = json_decode($resp, true);
                    $status = $respArr['outboundSMSMessageRequest']['deliveryInfoList']['deliveryInfo'][0]['deliveryStatus'];
                    $mesg = new Message($sender, $destination, $message, $status);
                    $this->dao->saveMessage($mesg, 0);
                } catch (\Throwable $th) {
                    $this->logger->error("Error occurred sending message: $sender|$message|$destination|$opId|resp: $resp\n" . $th->getMessage());
                }
            }
        }
    }

    public function getActiveContents(string $serviceId): array
    {
        return $this->dao->getActiveContents($serviceId);
    }

    public function getReadyContents(): array
    {
        return $this->dao->getReadyContents();
    }

    public function getbenchedMsisdns(string $serviceId, string $offerName): array
    {
        return $this->dao->getbenchedMsisdns($serviceId, $offerName);
    }
    public function getLatestRefIds(string $msisdn): array
    {
        return $this->dao->getLatestRefIds($msisdn);
    }

    public function saveToBench(string $msisdn, string $serviceId, string $offerName)
    {
        $offerId = substr(trim($offerName), -1, 1);
        $this->dao->saveToBench($msisdn, $serviceId, $offerId);
    }
}

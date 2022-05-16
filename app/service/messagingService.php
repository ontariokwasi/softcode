<?php
require_once __DIR__ . '/../client/messagingClient.php';
require_once __DIR__ . '/../service/authService.php';
require_once __DIR__ . '/../constants.php';
require_once __DIR__ . '/../dao/messagingDao.php';
require_once __DIR__ . '/../model/message/message.php';

interface MessagingService
{
    public function send(string $sender, string $message, string $destination, string $opId): string;
    public function getActiveContents(string $serviceId): array;
    public function getLatestRefIds(string $msisdn): array;
    public function saveToBench(string $msisdn, string $serviceId);
}

class VodafoneMessagingService implements MessagingService
{
    private AuthService $authService;
    private MessagingClient $client;
    private MessagingDao $dao;
    public function __construct()
    {
        $this->authService = new VodafoneAuthService();
        $this->client = new VodafoneMessagingCleint();
        $this->dao = new MessagingDao();
    }
    public function send(string $sender, string $message, string $destination, string $opId, string $contentRef = '0'): string
    {
        $token = $this->authService->getAccessToken($opId);
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

    public function getActiveContents(string $serviceId): array
    {
        return $this->dao->getActiveContents($serviceId);
    }

    public function getLatestRefIds(string $msisdn): array
    {
        return $this->dao->getLatestRefIds($msisdn);
    }

    public function saveToBench(string $msisdn, string $serviceId)
    {
        $this->dao->saveToBench($msisdn, $serviceId);
    }
}

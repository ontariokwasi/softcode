<?php
require_once __DIR__ . '/../api_provider/ApiProvider.php';
require_once __DIR__ . '/../common/logger.php';
interface MessagingClient
{
    public function send($sender, $message, $destination, $msgId, $token);
}

class VodafoneMessagingCleint implements MessagingClient
{
    private Logger $logger;
    public function __construct()
    {
        $this->logger = Logger::getDefaultInstanceWithId($this);
    }

    public function send($sender, $message, $destination, $msgId, $token)
    {
        $url = "https://sdp.vodafone.com.gh/vfgh/gw/messaging/v1/outbound";
        $body = json_encode(array("address" => [$destination], "senderAddress" => $sender, "outboundSMSTextMessage" => array("message" => $message), "clientCorrelator" => "$msgId", "receiptRequest" => array()), true);
        $headers = ["content-type:application/json", "Authorization:Bearer $token"];
        $this->logger->debug("Sending message: $msgId->" . $body);
        $resp = ApiProvider::send(new ApiRequest($url, $headers, $body), HttpMethod::POST);
        $this->logger->debug("Received response: $msgId->" . $body);
        return $resp;
    }
}

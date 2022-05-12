<?php
require_once __DIR__ . '/../client/messagingClient.php';
require_once __DIR__ . '/../service/authService.php';
require_once __DIR__ . '/../constants.php';

interface MessagingService
{
    public function send(string $sender, string $message, string $destination, string $opId): string;
}

class VodafoneMessagingService implements MessagingService
{
    private AuthService $authService;
    private MessagingClient $client;
    public function __construct()
    {
        $this->authService = new VodafoneAuthService();
    }
    public function send(string $sender, string $message, string $destination, string $serviceId): string
    {
        $token = $this->authService->getAccessToken($serviceId);
        if ($token) {
            return $this->client->send($sender, $message, $destination, uniqid(), $token);
        }
        return "retrieving access token for $serviceId failed!";
    }
}

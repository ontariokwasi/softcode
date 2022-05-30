<?php
require_once __DIR__ . '/../dao/authDao.php';
require_once __DIR__ . '/../client/AuthClient.php';
require_once __DIR__ . '/../common/logger.php';
interface  AuthService
{
    public function getAccessToken(int $serviceId): string|false;
}
class VodafoneAuthService implements AuthService
{
    private Logger $logger;
    public function __construct($authDao = new AuthDao(), $authClient = new AuthClient())
    {
        $this->authDao = $authDao;
        $this->authClient = $authClient;
        $this->logger = new Logger();
    }
    public function getAccessToken(int $serviceId): string|false
    {
        $this->logger->debug("AUTHSERVICE:: retrieving token for $serviceId");
        if ($authData = $this->authDao->getToken($serviceId, 'VODAFONE')) {
            $id = $authData['id'];
            $username = $authData['username'];
            $password = $authData['password'];
            $token = $authData['token'];
            $expiryDate = $authData['expiryDate'];
            $validUntil = new DateTime($expiryDate);
            if ($validUntil > new DateTime('now')) {
                $this->logger->debug("AUTHSERVICE:: Found a valid token $token");
                return $token;
            } else {
                $this->logger->debug("AUTHSERVICE:: Found token $token but is invalid, retrieving via API call");
                $accessToken = $this->authClient->getAccessToken(ClientType::VODAFONE, new AuthRequest($username, $password));
                if ($accessToken) {
                    $token = $accessToken->token;
                    $this->logger->debug("AUTHSERVICE:: Generated a new token $token, saving for re-use");
                    $this->authDao->saveToken($id, $token, $accessToken->getExpiry());
                    return $token;
                }
                $this->logger->debug("AUTHSERVICE:: Failed to retrieve token via API call with error: $accessToken");
                return false;
            }
        }
        $this->logger->debug("AUTHSERVICE:: Failed to retrieve token from DB");
        return false;
    }
}

<?php
require_once __DIR__ . '/../dao/authDao.php';
require_once __DIR__ . '/../client/AuthClient.php';
interface  AuthService
{
    public function getAccessToken(int $serviceId): AccessToken|false;
}
class VodafoneAuthService implements AuthService
{
    public function __construct($authDao = new AuthDao(), $authClient = new AuthClient())
    {
        $this->authDao = $authDao;
        $this->authClient = $authClient;
    }
    public function getAccessToken(int $serviceId): AccessToken|false
    {
        if ($authData = $this->authDao->getToken($serviceId, 'VODAFONE')) {
            $id = $authData['id'];
            $username = $authData['username'];
            $password = $authData['password'];
            $token = $authData['token'];
            $expiryDate = $authData['expiryDate'];
            $validUntil = new DateTime($expiryDate);
            if ($validUntil > new DateTime('now')) {
                return $token;
            } else {
                $accessToken = $this->authClient->getAccessToken(ClientType::VODAFONE, new AuthRequest($username, $password));
                if ($accessToken) {
                    $token = $accessToken->token;
                    $this->authDao->saveToken($id, $token, $accessToken->getExpiry());
                    return $token;
                }
                return false;
            }
        }
        return false;
    }
}

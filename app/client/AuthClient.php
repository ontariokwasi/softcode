<?php
require_once __DIR__ . '/../model/auth/AuthRequest.php';
require_once __DIR__ . '/../model/auth/Token.php';
require_once __DIR__ . '/../api_provider/ApiProvider.php';
class AuthClient
{
    public function getAccessToken(ClientType $clientType, AuthRequest $authRequest): AccessToken|false
    {
        return  match ($clientType) {
            ClientType::MTN => false,
            ClientType::VODAFONE => $this->telenityAuthProvider($authRequest),
            ClientType::TIGO => false
        };
    }

    private function telenityAuthProvider(AuthRequest $authRequest)
    {
        $url = "https://sdp.vodafone.com.gh/oauth/token?grant_type=client_credentials";
        $headers = array($authRequest->getBasicAuthHeader(), 'content-type: application/x-www-form-urlencoded');
        $apiRequest = new ApiRequest($url, $headers, "");
        if ($resp = ApiProvider::post($apiRequest)) {
            $jsonData = json_decode($resp, true);
            if (array_key_exists('access_token', $jsonData)) {
                $expiresIn = $jsonData['expires_in'] - 600;
                $expiryDate = (new DateTime())->add(new DateInterval('PT' . $expiresIn . 'S'));
                return  new AccessToken($jsonData['access_token'], $expiryDate);
            } else {
                // log error occured!
            }
        }
        return false;
    }
}

enum ClientType
{
    case VODAFONE;
    case MTN;
    case TIGO;
}

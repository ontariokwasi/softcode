<?php
require_once __DIR__ . '/../api_provider/ApiProvider.php';

interface ApiSubscriptionClient
{
    public function subscribe();
    public function unsubscribe();
}

class TigoSubscriptionClient implements ApiSubscriptionClient
{
    private static string $endpoint = 'https://tigo.timwe.com/gh/ma/api/external/v1/subscription/optin/3934';
    // private static string $partnerRoleId = '3934';
    private static string $partnerServiceId = "3833";
    private static string $preSharedKey = "rJWGWGbZmoh3ZCYt";
    // private static string $IvParamSpecKey = "yzXzUhr3OAt1A47g7zmYxw==";
    private static string $apiKey = '58d8a00707d243eb86f2587c77ec4f37';
    public function subscribe()
    {
        $body = '{"productId":12344,"pricepointId":54344,"mcc":"268","mnc":"01","entryChannel":"SMS","msisdn":"351913454433","largeAccount":"62900","transactionUUID":"b8972370-c0e7-11e5-9912-ba0be0483c18","text":"OPTIN","tags":[""]}';
        $request = new ApiRequest($this::$endpoint, $this->getHeaders(), $body);
        $resp = ApiProvider::post($request);
    }

    public function unsubscribe()
    {
    }

    private function getAuth(): string
    {
        $strToEncrypt = $this::$partnerServiceId . "#" . number_format(round(microtime(true) * 1000), 0, '', '');
        $iv  = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-128-ECB'));
        $auth = base64_encode(openssl_encrypt($strToEncrypt, "AES-128-ECB", $this::$preSharedKey, OPENSSL_RAW_DATA, $iv));
        return $auth;
    }

    private function getHeaders(): array
    {
        return  [
            'Content-Type: application/json',
            'authentication: ' . $this->getAuth(),
            'apikey: ' . $this::$apiKey,
            'external-tx-id: ' . uniqid()
        ];
    }
}

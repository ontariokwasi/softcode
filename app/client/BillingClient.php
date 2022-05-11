<?php
require_once __DIR__ . '/../api_provider/ApiProvider.php';
require_once __DIR__ . '/../model/billing/billingModels.php';
interface BillingClient
{
    public function chargeSubscriber(OutboundBillingRequest $request, String $token): BillingResponse;
}

class VodafoneBillingClient implements BillingClient
{

    public function chargeSubscriber(OutboundBillingRequest $request, String $token): BillingResponse
    {
        $url = "https://sdp.vodafone.com.gh/vfgh/gw/charging/v1/charge";
        $body = $request->encode();
        $headers = ['content-type:application/json', 'Authorization:Bearer ' . $token];
        $apiRequest = new ApiRequest($url, $headers, $body);
        $resp = ApiProvider::send($apiRequest, HttpMethod::PUT);
        $response = new BillingResponse();
        $response->network = "VODAFONE";
        if ($resp) {
            $respArr = json_decode($resp, true);
            if (isset($respArr["transactionId"])) {
                $response->hasError = false;
                $response->message = "SUCCESS";
            } else {
                $response->hasError = true;
                $response->message = isset($respArr['message']) ? $respArr['message'] : $respArr['errorMsg'];
            }
        } else {
            $response->hasError = true;
            $response->message = "Unknown Error";
        }
        return $response;
    }
}

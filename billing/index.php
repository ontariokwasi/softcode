<?php
require_once '../app/service/billingService.php';
require_once '../app/client/BillingClient.php';

$request = file_get_contents("php://input");
// $request = $argv[1];
if ($json = json_decode($request, true)) {
    $network = $json["network"];
    $msisdn = $json["msisdn"];
    $message = $json["message"];
    $serviceId = $json["serviceId"];
    $shortcode = $json["shortcode"];
    $amount = $json["amount"];
    $reqId = $json["requestId"];

    $inbound = new InBoundBillingRequest($serviceId, $amount, $msisdn, $message, $reqId, $shortcode);
    if (strtoupper($network) == "VODAFONE") {

        $client = new VodafoneBillingClient();
        $auth = new VodafoneAuthService();
        $messenger = new VodafoneMessagingCleint();
    }
    $billingService = new BillingService($client, $auth, $messenger);
    echo $billingService->charge($inbound);
} else {
    echo "Invalid request";
}

// '{"network":"VODAFONE","msisdn":"233241477600","serviceId":"100","message":"world", "shortcode":"424","amount":0.50,"requestId":"1234"}'
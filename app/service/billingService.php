<?php
require_once __DIR__ . '/../model/billing/billingModels.php';
require_once __DIR__ . '/../client/BillingClient.php';
require_once __DIR__ . '/../client/messagingClient.php';
require_once __DIR__ . '/../service/authService.php';
require_once __DIR__ . '/../dao/billingDao.php';
class BillingService
{
    private BillingDao $dao;
    private MessagingClient $messenger;
    function __construct(BillingClient $client, AuthService $authService, MessagingClient $messagingClient)
    {
        $this->client = $client;
        $this->authService = $authService;
        $this->messenger = $messagingClient;
        $this->dao = new BillingDao();
    }
    function charge(InBoundBillingRequest $request)
    {

        $outRequest = new VodafoneOutBoundBillingRequest($request->msisdn, $request->amount, $request->reqId);
        $token = $this->authService->getAccessToken($request->serviceId);
        if ($token) {
            $resp = $this->client->chargeSubscriber($outRequest, $token);
            if ($resp->hasError == false) {
                $this->messenger->send($request->shortcode, $request->message, $request->msisdn, uniqid(), $token);
            }
            $this->dao->save($request->msisdn, $request->amount, $resp->network, $resp->message);
            return $resp->__toString();
        } else {
            // log failed to load token
            return "error obtaining access token";
        }
    }
}

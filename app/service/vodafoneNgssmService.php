<?php
require_once __DIR__ . '/billingService.php';
require_once __DIR__ . '/messagingService.php';
require_once __DIR__ . '/subscriptionService.php';
require_once __DIR__ . '/../common/logger.php';
class VodafoneNGSSMService
{
    private SubscriptionService $subscriptionService;
    private BillingService $billingService;
    private Logger $logger;
    public function __construct()
    {
        $this->messenger = new VodafoneMessagingService();
        $this->subscriptionService = (new SubscriptionService("VODAFONE"));
        $this->billingService = new VodafoneBillingService();
        $this->logger = Logger::getDefaultInstanceWithId($this);
    }
    public function subscribe(string $msisdn, string $opId, string $channel, int $offerNumber): string
    {
        if ($offerNumber == 1) {
            $this->subscriptionService->subscribe($msisdn, $opId, $channel);
        }
        return $this->successResponse();
    }
    public function unsubscribe(string $msisdn, string $opId, string $channel, int $offerNumber): string
    {
        if ($offerNumber == 1) {
            $this->subscriptionService->unsubscribe($msisdn, $opId, $channel);
        }
        return $this->successResponse();
    }

    public function recordSuccessBilling($opId, $msisdn, $amount)
    {
        $product = $this->subscriptionService->getProduct($opId, "VODAFONE");
        if ($product !== false) {
            $shortcode = $product->shortcode;
            $serviceId = $product->id;
            $this->billingService->recordSuccessbilling($shortcode, $serviceId, $msisdn, $amount, "VODAFONE");
            return $this->successResponse();
        }
        return $this->failedResponse();
    }

    private function successResponse(string $message = "Request successfully processed", int $code = 200): string
    {
        http_response_code($code);
        return json_encode(array("status" => "SUCCESS", "message" => "$message"));
    }

    private function failedResponse(string $message = "Failed to process request", int $code = 500): string
    {
        http_response_code($code);
        return json_encode(array("status" => "FAILED", "message" => "$message"));
    }
}

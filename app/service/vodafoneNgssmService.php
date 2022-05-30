<?php
require_once __DIR__ . '/billingService.php';
require_once __DIR__ . '/messagingService.php';
require_once __DIR__ . '/subscriptionService.php';
require_once __DIR__ . '/../common/logger.php';
class VodafoneNGSSMService
{
    private SubscriptionService $subscriptionService;
    private MessagingService $messenger;
    private BillingService $billingService;
    private Logger $logger;
    public function __construct()
    {
        $this->messenger = new VodafoneMessagingService();
        $this->subscriptionService = (new SubscriptionService("VODAFONE"));
        $this->billingService = new VodafoneBillingService();
        $this->logger = Logger::getDefaultInstanceWithId($this);
    }
    public function subscribe(string $msisdn, string $opId, string $channel): string
    {
        $this->subscriptionService->subscribe($msisdn, $opId, $channel);
        return $this->successResponse();
    }
    public function unsubscribe(string $msisdn, string $opId, string $channel): string
    {
        $this->subscriptionService->unsubscribe($msisdn, $opId, $channel);
        return $this->successResponse();
    }

    public function recordSuccessBilling($opId, $msisdn, $amount)
    {
        $product = $this->subscriptionService->getProduct($opId, "VODAFONE");
        if ($product !== false) {
            $shortcode = $product->shortcode;
            $serviceId = $product->id;
            $this->billingService->recordSuccessbilling($shortcode, $serviceId, $msisdn, $amount, "VODAFONE");
            $this->logger->debug("retrieving contents for $serviceId");
            if ($nextContent = $this->getContent($serviceId)) {
                $this->logger->debug("Found contents for $serviceId");
                $this->messenger->send($shortcode, $nextContent->body, $msisdn, $opId, $nextContent->id);
            } else {
                $this->logger->debug("No contents for $serviceId");
                $this->messenger->saveToBench($msisdn, $serviceId);
            }
            return $this->successResponse();
        }
        return $this->failedResponse();
    }

    private function getContent(string $serviceId)
    {
        global $msisdn;
        $contents = $this->messenger->getActiveContents($serviceId);
        $refIds = $this->messenger->getLatestRefIds($msisdn);
        if (count($contents) > 0) {
            $freshContents = array_filter($contents, function ($content) use ($refIds) {
                return !in_array($content->id, $refIds);
            });
            if (count($freshContents) > 0) {
                return array_pop($freshContents);
            }
        }
        return null;
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

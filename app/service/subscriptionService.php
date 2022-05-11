<?php
require_once __DIR__ . '/../constants.php';
require_once __DIR__ . '/../client/messagingClient.php';
require_once __DIR__ . '/../dao/productsDao.php';
require_once __DIR__ . '/../dao/subscriptionDao.php';
require_once __DIR__ . '/../service/authService.php';
require_once __DIR__ . '/../service/messagingService.php';

class SubscriptionService
{
    private NetworkProvider $network;
    private ProductsDao $productsDao;
    private SubscriptionsDao $subscriptionsDao;
    private MessagingService $messenger;
    public function __construct(string $network)
    {
        $this->productsDao = new ProductsDao(null);
        $this->subscriptionsDao = new SubscriptionsDao(null);
        $this->init($network);
    }

    public function subscribe($msisdn, $shortcode, $opId, $channel): string
    {
        $networkProvider = $this->network;
        $product = $this->productsDao->getProductByOpId($opId, $networkProvider);
        if ($product) {
            $subscriber = new Subscriber($msisdn, $shortcode, $networkProvider, $product->id, $product->name, $channel);
            $isActive = $this->subscriptionsDao->isActive($subscriber);
            $subsMessage = "Thank you for subscribing to the service";
            if ($isActive == false) {
                $this->subscriptionsDao->reactivate($subscriber);
            } else if ($isActive == true) {
                // already subscribed to service
                $subsMessage = "You have already subscribed to " . $product->name;
            } else {
                // fresh subscriber
                $this->subscriptionsDao->subscribe($subscriber);
            }
            return $this->messenger->send($shortcode, $subsMessage, $msisdn, $product->opId);
        } else {
            // product not found
            // send message using default product details for token
            return "Unknown service";
        }
    }

    public function unsubscribe($msisdn, $shortcode, $opId, $channel = "")
    {
        $networkProvider = $this->network;
        $product = $this->productsDao->getProductByOpId($opId, $networkProvider);
        if ($product) {
            $subscriber = new Subscriber($msisdn, $shortcode, $networkProvider, $product->id, $product->name, $channel);
            $isActive = $this->subscriptionsDao->isActive($subscriber);
            $unSubsMessage = "You have been unscribed from the service, hope to see you again!";
            if ($isActive == true) {
                $this->subscriptionsDao->deactivate($subscriber);
            } else {
                // fresh subscriber
                $unSubsMessage = "You have not subscribed to the service yet, please subscribe first.";
            }
            return $this->messenger->send($shortcode, $unSubsMessage, $msisdn, $product->opId);
        }
    }

    private function init(string $network): void
    {
        $networkProvider = NetworkProvider::fromString($network);
        switch ($networkProvider) {
            case NetworkProvider::VODAFONE:
                $this->messenger = new VodafoneMessagingService();
                break;

            default:
                throw new ErrorException("Unimplemented network $network");
                break;
                $this->network = $networkProvider;
        }
    }
}

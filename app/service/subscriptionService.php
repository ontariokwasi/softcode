<?php
require_once __DIR__ . '/../constants.php';
require_once __DIR__ . '/../dao/productsDao.php';
require_once __DIR__ . '/../dao/subscriptionDao.php';
require_once __DIR__ . '/../service/authService.php';
require_once __DIR__ . '/../service/messagingService.php';
require_once __DIR__ . '/../common/logger.php';

class SubscriptionService
{
    private NetworkProvider $network;
    private ProductsDao $productsDao;
    private SubscriptionsDao $subscriptionsDao;
    private MessagingService $messenger;
    private Logger $logger;
    public function __construct(string $network)
    {
        $this->productsDao = new ProductsDao(null);
        $this->subscriptionsDao = new SubscriptionsDao(null);
        $this->network = NetworkProvider::fromString($network);
        $this->logger = new Logger();
        $this->init();
    }

    public function subscribe($msisdn, $opId, $channel): string
    {
        $networkProvider = $this->network;
        $this->logger->debug("$msisdn | $opId | received for subscription");
        $product = $this->productsDao->getProductByOpId($opId, $networkProvider);
        if ($product) {
            $subscriber = new Subscriber($msisdn, $product->shortcode, $networkProvider, $product->id, $product->name, $channel);
            $isActive = $this->subscriptionsDao->isActive($subscriber);
            $this->logger->debug("Is active subscriber: " . $isActive);
            $subsMessage = "Thank you for subscribing to the service";
            if ($isActive === false) {
                $this->subscriptionsDao->reactivate($subscriber);
            } else if ($isActive === true) {
                // already subscribed to service
                $subsMessage = "You have already subscribed to " . $product->name;
            } else {
                // fresh subscriber
                $res = $this->subscriptionsDao->subscribe($subscriber);
                if ($res == true) {
                    $this->logger->info("successfully subscribed $msisdn to " . $product->name);
                } else {
                    $this->logger->error("Failed to subscribe $msisdn to " . $product->name);
                }
            }
            return $this->messenger->send($product->shortcode, $subsMessage, $msisdn, $product->opId);
        } else {
            // product not found
            // send message using default product details for token
            return "Unknown service";
        }
    }

    public function unsubscribe($msisdn, $opId, $channel = "")
    {
        $networkProvider = $this->network;
        $product = $this->productsDao->getProductByOpId($opId, $networkProvider);
        if ($product) {
            $subscriber = new Subscriber($msisdn, $product->shortcode, $networkProvider, $product->id, $product->name, $channel);
            $isActive = $this->subscriptionsDao->isActive($subscriber);
            $unSubsMessage = "You have been unscribed from the service, hope to see you again!";
            if ($isActive == true) {
                $this->subscriptionsDao->deactivate($subscriber);
            } else {
                // fresh subscriber
                $unSubsMessage = "You have not subscribed to the service yet, please subscribe first.";
            }
            return $this->messenger->send($product->shortcode, $unSubsMessage, $msisdn, $product->opId);
        }
    }

    public function getProduct(string $opId, string $network): Product|bool
    {
        return $this->productsDao->getProductByOpId($opId, NetworkProvider::fromString($network));
    }

    private function init(): void
    {
        switch ($this->network) {
            case NetworkProvider::VODAFONE:
                $this->messenger = new VodafoneMessagingService();
                break;

            default:
                throw new ErrorException("Unimplemented network " . $this->network->name);
                break;
        }
    }
}

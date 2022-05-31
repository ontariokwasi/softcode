<?php
require_once __DIR__ . '/../client/messagingClient.php';
require_once __DIR__ . '/../service/authService.php';
require_once __DIR__ . '/../constants.php';
require_once __DIR__ . '/../dao/messagingDao.php';
require_once __DIR__ . '/../dao/productsDao.php';
require_once __DIR__ . '/../model/message/message.php';
require_once __DIR__ . '/../model/subs/product.php';
require_once __DIR__ . '/../common/logger.php';

class SchedulerService
{
    private MessagingDao $dao;
    private ProductsDao $productsDao;
    private Logger $logger;
    public function __construct()
    {
        $this->dao = new MessagingDao();
        $this->productsDao = new ProductsDao(null);
        $this->logger = Logger::getDefaultInstanceWithId($this);
    }

    public function scheduleContent(string $job): string
    {
        $this->logger->debug("job received: " . $job);
        if ($json = json_decode($job, true)) {
            if (!array_key_exists('serviceName', $json)) {
                throw new Error("serviceName is required!");
            }
            $product = $this->productsDao->getProductByName(str_replace(" ", "", $json['serviceName']));
            $serviceId = $product->id;
            $serviceName = $product->name;
            $shortcode = $product->shortcode;
            $message = urldecode($json['message']);
            $scheduledBy = $json['scheduledBy'];
            $schedDate = $json['schedDate'];
            $content = new Content("", $serviceId, $serviceName, $shortcode, $scheduledBy, $message, new DateTime($schedDate));
            if ($this->dao->scheduleContent($content)) {
                return json_encode(array("status" => "OK"));
            }
        } else {
            $this->logger->debug("Failed to decode job");
        }
        throw new Error("Failed to schedule content");
    }
}

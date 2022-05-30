<?php
require_once __DIR__ . '/connector.php';
require_once __DIR__ . '/../model/subs/subscriber.php';
require_once __DIR__ . '/../common/logger.php';

class SubscriptionsDao
{
    private mysqli $connector;
    public readonly string $table;
    private Logger $logger;
    public function __construct(?mysqli $connector)
    {
        global $con;
        $this->connector = isset($connector) ? $connector : $con;
        $this->table = "subscriptions";
        $this->logger = Logger::getDefaultInstanceWithId($this);
    }

    public function subscribe(Subscriber $subscriber): bool
    {
        $msisdn = $subscriber->msisdn;
        $serviceId = $subscriber->serviceId;
        $network = $subscriber->network->name;
        $shortcode = $subscriber->shortcode;
        $serviceName = $subscriber->serviceName;
        $channel = $subscriber->channel;
        $stmt = "INSERT INTO " .
            $this->table .
            "(msisdn, shortcode, service_name, service_id, network, status, channel, subs_date)" .
            " VALUES ('$msisdn', '$shortcode','$serviceName', '$serviceId', '$network', 'ACTIVE', '$channel', now())";
        $this->logger->debug("Query->" . $stmt);
        return mysqli_query($this->connector, $stmt);
    }

    public function deactivate(Subscriber $subscriber): bool
    {
        $msisdn = $subscriber->msisdn;
        $serviceId = $subscriber->serviceId;

        $stmt = "UPDATE " . $this->table .
            " SET status='INACTIVE'" .
            " WHERE msisdn='$msisdn' AND service_id='$serviceId'";
        return mysqli_query($this->connector, $stmt);
    }

    public function reactivate(Subscriber $subscriber)
    {
        $msisdn = $subscriber->msisdn;
        $serviceId = $subscriber->serviceId;

        $stmt = "UPDATE " . $this->table .
            " SET status='ACTIVE'" .
            " WHERE msisdn='$msisdn' AND service_id='$serviceId'";
        return mysqli_query($this->connector, $stmt);
    }
    public function isActive(Subscriber $subscriber)
    {
        $msisdn = $subscriber->msisdn;
        $serviceId = $subscriber->serviceId;

        $stmt = "SELECT id, status FROM " . $this->table .
            " WHERE msisdn='$msisdn' AND service_id='$serviceId'";
        $this->logger->debug("Query->" . $stmt);
        $q = mysqli_query($this->connector, $stmt);
        if ($row = mysqli_fetch_assoc($q)) {
            return strtoupper($row['status']) == 'ACTIVE';
        }
        return null;
    }
}

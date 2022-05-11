<?php
require_once __DIR__ . '/connector.php';
require_once __DIR__ . '/../model/subs/subscriber.php';

class SubscriptionsDao
{
    private mysqli $connector;
    public readonly string $table;
    public function __construct(?mysqli $connector)
    {
        global $con;
        $this->connector = isset($connector) ? $connector : $con;
        $this->table = "subscriptions";
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
        return mysqli_query($this->connector, $stmt);
    }

    public function deactivate(Subscriber $subscriber): bool
    {
        $msisdn = $subscriber->msisdn;
        $serviceId = $subscriber->serviceId;

        $stmt = "UPDATE " . $this->table .
            " SET status=INACTIVE" .
            " WHERE msisdn='$msisdn' AND service_id='$serviceId'";
        return mysqli_query($this->connector, $stmt);
    }

    public function reactivate(Subscriber $subscriber)
    {
        $msisdn = $subscriber->msisdn;
        $serviceId = $subscriber->serviceId;

        $stmt = "UPDATE " . $this->table .
            " SET status=ACTIVE" .
            " WHERE msisdn='$msisdn' AND service_id='$serviceId'";
        return mysqli_query($this->connector, $stmt);
    }
    public function isActive(Subscriber $subscriber)
    {
        $msisdn = $subscriber->msisdn;
        $serviceId = $subscriber->serviceId;

        $stmt = "SELECT id, status FROM " . $this->table .
            " WHERE msisdn='$msisdn' AND service_id='$serviceId'";
        $q = mysqli_query($this->connector, $stmt);
        if ($row = mysqli_fetch_assoc($q)) {
            return strtoupper($row['status']) == 'ACTIVE';
        }
        return null;
    }
}

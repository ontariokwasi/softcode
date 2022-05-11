<?php
require_once __DIR__ . '/../../constants.php';
class Subscriber
{
    public readonly string $msisdn;
    public readonly NetworkProvider $network;
    public readonly string $serviceId;
    public readonly string $shortcode;
    public readonly string $serviceName;
    public readonly string $channel;


    public function __construct(
        string $msisdn,
        string $shortcode,
        NetworkProvider $network,
        string $serviceId,
        string $serviceName,
        string $channel

    ) {
        $this->msisdn = $msisdn;
        $this->shortcode = $shortcode;
        $this->network = $network;
        $this->serviceId = $serviceId;
        $this->serviceName = $serviceName;
        $this->channel = $channel;
    }
}

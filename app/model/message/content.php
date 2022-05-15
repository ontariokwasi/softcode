<?php
class Content
{
    public string $id;
    public readonly string $serviceId;
    public readonly string $serviceName;
    public readonly string $shortcode;
    public readonly string $scheduledBy;
    public readonly string $body;
    public readonly DateTime $schedDate;

    public function __construct(
        string $id,
        string $serviceId,
        string $serviceName,
        string $shortcode,
        string $scheduledBy,
        string $body,
        DateTime $schedDate,
    ) {
        $this->id = $id;
        $this->serviceId = $serviceId;
        $this->serviceName = $serviceName;
        $this->schedDate = $schedDate;
        $this->body = $body;
        $this->scheduledBy = $scheduledBy;
        $this->shortcode = $shortcode;
    }
    public function formatSchedDate(): string
    {
        return $this->schedDate->format("Y-m-d H:i:s");
    }
}

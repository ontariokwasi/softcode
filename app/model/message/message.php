<?php
class Message
{
    public readonly string $sender;
    public readonly string $destination;
    public readonly string $body;
    public readonly string $status;

    public function __construct(string $sender, string $destination, string $body, string $status = 'PENDING')
    {
        $this->sender = $sender;
        $this->body = $body;
        $this->destination = $destination;
        $this->status = $status;
    }
}

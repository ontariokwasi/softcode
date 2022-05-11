<?php
class Product
{
    public readonly string $id;
    public readonly string $name;
    public readonly string $opId;
    public readonly string $network;

    public function __construct(
        string $id,
        string $name,
        string $opId,
        string $network
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->opId = $opId;
        $this->network = $network;
    }
}

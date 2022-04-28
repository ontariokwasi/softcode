<?php
interface OutboundBillingRequest
{
    public function encode();
}
class VodafoneOutBoundBillingRequest implements OutboundBillingRequest
{

    public function __construct(String $msisdn, float $amount, String $clientChargeTransactionId)
    {
        $this->amount = $amount;
        $this->clientChargeTransactionId = $clientChargeTransactionId;
        $this->clientRequestId = uniqid();
        $this->msisdn = $msisdn;
        $this->unit = 1;
    }

    public function __toString()
    {
        return json_encode(array(
            'amount' => $this->amount,
            'clientChargeTransactionId' => $this->clientChargeTransactionId,
            'clientRequestId' => $this->clientRequestId,
            'msisdn' => $this->msisdn,
            'unit' => $this->unit,
            'description' => '424 on_demand'
        ));
    }

    public function encode()
    {
        return $this->__toString();
    }
}

class InBoundBillingRequest
{
    public function __construct(int $serviceId, float $amount, String $msisdn, String $message, $reqId, String $shortcode)
    {
        $this->serviceId = $serviceId;
        $this->amount = $amount;
        $this->msisdn = $msisdn;
        $this->message = $message;
        $this->reqId = $reqId;
        $this->shortcode = $shortcode;
    }
}

class BillingResponse
{
    public bool $hasError;
    public String $message;

    public function __toString()
    {
        return json_encode(["hasError" => $this->hasError, "message" => $this->message]);
    }
}

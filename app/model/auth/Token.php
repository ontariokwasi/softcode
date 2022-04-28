<?php
final class AccessToken
{
    public function __construct(String $token, DateTime $expiryDate)
    {
        $this->token = $token;
        $this->expiry = $expiryDate;
    }
    public function getToken()
    {
        return $this->token;
    }
    public function getExpiry()
    {
        return $this->expiry->format('Y-m-d H:i:s');
    }

    public function __toString()
    {
        return json_encode(array("token" => $this->token, "valid_until" => $this->expiry->format("Y-m-d H:i:s")));
    }
    function toJson()
    {
        return json_encode(array("token" => $this->token, "valid_until" => $this->expiry->format("Y-m-d H:i:s")));
    }
}

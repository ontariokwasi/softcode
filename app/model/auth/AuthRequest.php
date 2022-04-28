<?php
final class AuthRequest
{
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function getBasicAuthHeader()
    {
        $basic = $this->username . ":" . $this->password;
        return "Authorization: Basic " . base64_encode($basic);
    }
}

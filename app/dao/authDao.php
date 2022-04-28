<?php
require_once __DIR__ . '/connector.php';

class AuthDao
{
    public function getToken(int $serviceId, String $network)
    {
        global $con;
        $sel = "SELECT id, username, password, token, expiryDate FROM tokens WHERE serviceId='$serviceId' AND network='$network'";
        $q = mysqli_query($con, $sel);
        if ($authData = mysqli_fetch_assoc($q)) {
            return $authData;
        } else {
            return false;
        }
    }

    public function saveToken(int $id, String $token, String $expiryDate)
    {
        global $con;
        $ins = "UPDATE tokens SET token='$token', expiryDate='$expiryDate' WHERE id='$id'";
        mysqli_query($con, $ins);
    }
}

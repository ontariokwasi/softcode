<?php
require_once __DIR__ . '/connector.php';

class BillingDao
{

    public function save($msisdn, $amount, $network, $status)
    {
        global $con;
        $ins = "INSERT INTO billing(`msisdn`,`amount`,`network`,`status`,`timestamp`) VALUES('$msisdn', '$amount', '$network', '$status', now());";
        mysqli_query($con, $ins);
    }
}

<?php
require_once __DIR__ . '/connector.php';

class BillingDao
{

    public function save($shortcode, $serviceId, $msisdn, $amount, $network, $status)
    {
        global $con;
        $ins = "INSERT INTO billing(`shortcode`,`msisdn`,`service_id`,`amount`,`network`,`status`,`timestamp`) VALUES('$shortcode','$msisdn','$serviceId', '$amount', '$network', '$status', now());";
        mysqli_query($con, $ins);
    }
}

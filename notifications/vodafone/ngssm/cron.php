<?php
include __DIR__ . '/../../../app/dao/connector.php';
include __DIR__ . '/../../../app/service/messagingService.php';
error_reporting(E_ALL);
const SERVICE_STMT = "SELECT * FROM contents.telenity_services;";


if ($services = select(SERVICE_STMT)) {
    foreach ($services as $service) {
        $serviceName =  $service['service_name'];
        $serviceId =  $service['service_Id'];
        $shortcode =  $service['shortcode'];
        $originalName =  $service['original_service_name'];
        $CONTENT_STMT =  "SELECT message FROM contents.available_contents WHERE service_type = '$originalName';";
        $contents = select($CONTENT_STMT);
        $vodaMessenger = new VodafoneMessagingService();
        foreach ($contents as  $key => $content) {
            $message = urldecode($content['message']);
            $msisdns = getBenchMsidns($originalName, $key);
            if (count($msisdns) > 0) {
                $vodaMessenger->sendBulk($shortcode, $message, $msisdns, $serviceId);
            }
        }
    }
} else {
    echo ("ERROR: " . mysqli_error($con));
}


function select(string $stmt): array
{
    global $con;
    $q = mysqli_query($con, $stmt);
    return mysqli_fetch_all($q, MYSQLI_ASSOC);
}

function getBenchMsidns($serviceName, $offerNumber): array
{
    $msisdns = array();
    $today = date('Y-m-d');
    $fileName = __DIR__ . "/bench/$serviceName/$today/$offerNumber";
    if (file_exists($fileName)) {
        $fc = file_get_contents($fileName);
        $msisdns = explode("\n", trim($fc));
        unlink($fileName);
    } elseif (is_dir(dirname($fileName))) {
        $benchDir = dirname($fileName);
        $files = scandir($benchDir);
        foreach ($files as $file) {
            // pick the first file that is not a . prefixed
            if (substr($file, 0, 1) != '.') {
                $fileName = "$benchDir/$file";
                $fc = file_get_contents($fileName);
                $msisdns = explode("\n", trim($fc));
                unlink($fileName);
                break;
            }
        }
    }
    return $msisdns;
}

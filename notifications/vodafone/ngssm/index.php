<?php
require_once __DIR__ . '/../../../app/service/vodafoneNgssmService.php';
require_once __DIR__ . '/../../../app/common/logger.php';
try {
    $request = file_get_contents("php://input");
    // $request = $argv[1];
    if ($json = json_decode($request, true)) {
        $msisdn = $json['msisdn'];
        $state = strtoupper($json['state']);
        $serviceNotificationType = strtoupper($json['serviceNotificationType']);
        $serviceName = $json['serviceName'];
        $channelType = $json['channelType'];
        $chargedAmount = $json['chargedAmount'];
        $serviceId = $json['serviceId'];
        $offerName = $json['offerName'];

        $ngssmService = new VodafoneNGSSMService();
        if ($serviceNotificationType == "SUB") {
            echo $ngssmService->subscribe($msisdn, $serviceId, $channelType);
        } else if ($serviceNotificationType == "UNSUB") {
            echo $ngssmService->unsubscribe($msisdn, $serviceId, $channelType);
        }
        // finally check if it is a success charge
        if ($state == "ACTIVE") {
            $today = date('Y-m-d');
            $offerNumber = substr($offerName, -1);
            $dir =  __DIR__ . "/bench/$serviceName/$today";
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
            $filename = "$dir/$offerNumber";
            file_put_contents($filename, "$msisdn" . PHP_EOL, FILE_APPEND);
            echo $ngssmService->recordSuccessBilling($serviceId, $msisdn, $chargedAmount);
        }
        file_put_contents("logs-" . date('Y-m-d'), date('Y-m-dTH:i:s') . " " . json_encode($json) . PHP_EOL, FILE_APPEND);
    } else {
        echo '{"error":"Invalid request"}';
    }
} catch (\Throwable $th) {
    (new Logger())->error($th->getMessage() . "\n" . $th->getTraceAsString());
    http_response_code(500);
    echo "Error occured please try again or report this issue now";
}

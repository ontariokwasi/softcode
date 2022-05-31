<?php
error_reporting(E_ERROR | E_PARSE);
require_once '../app/service/schedulerService.php';

$request = file_get_contents('php://input');
header("content-type: application/json");
try {
    $scheduler = new SchedulerService();
    echo $scheduler->scheduleContent($request);
} catch (\Throwable $th) {
    echo json_encode(array("status" => "Error", "message" => $th->getMessage()));
}

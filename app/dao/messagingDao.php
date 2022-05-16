<?php

use phpDocumentor\Reflection\PseudoTypes\False_;

require_once __DIR__ . '/connector.php';
require_once __DIR__ . '/../model/message/content.php';
require_once __DIR__ . '/../model/message/message.php';
require_once __DIR__ . '/../common/logger.php';

class MessagingDao
{
    private mysqli $connector;
    private readonly string $jobsTable;
    private readonly string $messagesTable;
    private readonly string $activeContentsTable;
    private readonly string $benchTable;
    private Logger $logger;

    public function __construct()
    {
        global $con;
        $this->connector = $con;
        $this->jobsTable = "jobs";
        $this->messagesTable = "messages";
        $this->activeContentsTable = "active_contents";
        $this->benchTable = "bench";
        $this->logger = new Logger();
    }

    public function getActiveContents(string $serviceId): array
    {
        $stmt = "SELECT * FROM " . $this->activeContentsTable . " WHERE service_id='$serviceId'";
        $q = mysqli_query($this->connector, $stmt);
        $res = array();
        while ($row = mysqli_fetch_array($q)) {
            $content = new Content(
                $row['id'],
                $row['service_id'],
                $row['service_name'],
                '0000',
                $row['scheduled_by'],
                $row['content'],
                new DateTime($row['sched_date']),
            );
            array_push($res, $content);
        }
        return $res;
    }

    public function saveMessage(Message $message, string $contentRefId = '0'): bool
    {
        $source = $message->sender;
        $destination = $message->destination;
        $content = mysqli_real_escape_string($this->connector, substr($message->body, 0, 16));
        $status = $message->status;
        $stmt = "INSERT INTO " . $this->messagesTable . "(source, destination, content, content_ref_id, status) 
        VALUES('$source', '$destination', '$content', '$contentRefId','$status')";
        return mysqli_query($this->connector, $stmt);
    }

    public function getLatestRefIds(string $msisdn): array
    {
        $res = array();
        $stmt = "SELECT DISTINCT content_ref_id FROM " . $this->messagesTable . " WHERE destination='$msisdn' AND sent_date > CURDATE()";
        $q = mysqli_query($this->connector, $stmt);
        while ($row = mysqli_fetch_array($q)) {
            array_push($res, $row['content_ref_id']);
        }
        return $res;
    }

    public function saveTobench(string $msisdn, string $serviceId)
    {
        $stmt = "INSERT INTO " . $this->benchTable . "(msisdn, service_id) VALUES ('$msisdn', '$serviceId')";
        mysqli_query($this->connector, $stmt);
    }
}

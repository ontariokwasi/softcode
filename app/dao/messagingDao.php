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
    private Logger $logger;

    public function __construct()
    {
        global $con;
        $this->connector = $con;
        $this->jobsTable = "jobs";
        $this->messagesTable = "messages";
        $this->activeContentsTable = "active_contents";
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
                $row['shortcode'],
                $row['scheduled_by'],
                $row['content'],
                $row['service_id'],
            );
            array_push($res, $content);
        }
        return $res;
    }

    public function saveMessage(Message $message, string $contentRefId = '0'): bool
    {
        $source = $message->sender;
        $destination = $message->destination;
        $content = $message->body;
        $status = $message->status;
        $stmt = "INSERT INTO " . $this->messagesTable . "(source, destination, content, content_ref_id, status) 
        VALUES('$source', '$destination', '$content', '$contentRefId','$status')";
        return mysqli_query($this->connector, $stmt);
    }
}

<?php
require_once 'configManager.php';
class Logger
{
    private string $filename;
    private string $id;

    public function __construct(string $filename = Settings::APPLOGS, string $id = "LOGGER")
    {
        $this->filename = $filename;
        $this->id = strtoupper($id);
    }

    public static function getDefaultInstanceWithId(Object $object): Logger
    {
        return new Logger(Settings::APPLOGS, get_class($object));
    }

    private function write(string $log): void
    {
        file_put_contents($this->filename . "-" . date('Y-m-d'), date('Y-m-d H:i:s ') . $this->id . ' ' . $log . PHP_EOL, FILE_APPEND);
    }

    public function debug(string $log)
    {
        if (Settings::LOGLEVEL == "DEBUG") {
            $this->write("DEBUG: " . $log);
        }
    }
    public function info(string $log)
    {
        $this->write("INFO: " . $log);
    }
    public function error(string $log)
    {
        $this->write("ERROR: " . $log);
    }

    public function setId(string $id)
    {
        $this->id = $id;
    }
}

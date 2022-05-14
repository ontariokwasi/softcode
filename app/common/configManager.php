<?php
class ConfigManager
{
    private $envFile;
    private $config;
    public function __construct()
    {
        $this->envFile = __DIR__ . '/../.env';
        $this->config = [];
    }

    public function load(): void
    {
        $openFile = fopen($this->envFile, 'r');
        if ($openFile != false) {
            while ($content = fgets($openFile) !== false) {
                $contentArray = explode("=", $content);
                if (count($contentArray) > 1) {
                    $this->config[$contentArray[0]] = $contentArray[1];
                }
            }
            fclose($openFile);
        }
    }

    public function get(string $name): ?string
    {
        return $this->config[$name];
    }
}

class Settings
{
    const LOGLEVEL = "DEBUG";
    const APPLOGS = __DIR__ . "/../logs/app.log";
}

<?php
namespace App;

class Logger
{
    protected $logFile;
    protected $hostname;
    protected $requestTime;

    public function __construct()
    {
        $this->logFile = '/var/log/requests.log';
        $this->hostname = gethostname();
        $this->requestTime = date('Y-m-d H:i:s');
    }

    public function log(string $message): void
    {
        $str = "[{$this->requestTime}] [Host: {$this->hostname}] {$message}";
        file_put_contents($this->logFile, $str, FILE_APPEND);
    }
}
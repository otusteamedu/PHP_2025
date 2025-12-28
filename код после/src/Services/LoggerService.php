<?php

namespace App\Services;

use App\Interfaces\LoggerInterface;

class LoggerService implements LoggerInterface {
    private $logFile;
    
    public function __construct(string $logFile = 'log.txt') {
        $this->logFile = $logFile;
    }
    
    public function log(string $message): void {
        file_put_contents($this->logFile, $message . "\n", FILE_APPEND);
    }
}
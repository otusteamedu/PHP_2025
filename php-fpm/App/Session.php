<?php
namespace App;

class Session
{
    static public function updateSessionData(Logger $logger): void
    {
        if (!isset($_SESSION['request_count'])) {
            $_SESSION['request_count'] = 0;
        }
        $_SESSION['request_count']++;

        $logger->log("RequestCount: {$_SESSION['request_count']}\n");
    }
}
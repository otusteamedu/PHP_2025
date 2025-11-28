<?php

namespace Pryaniki\App;
class Auth
{
    public function __construct()
    {
        session_start();
        $this->increaseSessionCounter();
    }

    private function increaseSessionCounter(): void
    {
        if (!isset($_SESSION['counter'])) {
            $_SESSION['counter'] = 1;
        } else {
            $_SESSION['counter']++;
        }
    }

    public function getSessionId(): string
    {
        return session_id();
    }

    public function getContainerName(): string
    {
        return $_SERVER['HOSTNAME'];
    }

    public function getSessionCounter(): string
    {
        return $_SESSION['counter'];
    }
}
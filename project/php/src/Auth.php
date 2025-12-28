<?php

namespace App;

class Auth
{
    public function initSession(): void
    {
        ini_set('session.save_handler', 'redis');
        ini_set('session.save_path', 'tcp://redis:6379');
        session_start();
    }
}
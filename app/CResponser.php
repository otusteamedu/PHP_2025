<?php
declare(strict_types=1);

namespace MyApp;

class CResponser
{
    public function send(int $code): void
    {
        http_response_code($code);
    }
}
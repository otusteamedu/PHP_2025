<?php

namespace App\Service;

use App\Message\StatementRequestMessage;

interface QueueServiceInterface
{
    public function sendMessage(StatementRequestMessage $message): void;
    public function consume(callable $callback): void;
}
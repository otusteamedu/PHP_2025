<?php

namespace App\Application\UseCases\Commands;

interface QueueWorkerInterface
{
    public function sendMessage(array $request): void;
    public function runReceiver(): void;
}
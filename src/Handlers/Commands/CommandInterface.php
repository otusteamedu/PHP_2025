<?php

namespace App\Handlers\Commands;

interface CommandInterface
{
    public function execute(int $chatId, int $userId, string $args = ''): void;
}
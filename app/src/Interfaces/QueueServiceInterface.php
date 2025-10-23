<?php

namespace App\Interfaces;

interface QueueServiceInterface
{
    public function push(string $queue, array $job): string;
    public function pop(string $queue): ?array;
}
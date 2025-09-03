<?php

namespace App\Application\UseCases\Commands;

interface NotificationSenderInterface
{
    public function send(string $address, string $startDate, string $finishDate): void;
}
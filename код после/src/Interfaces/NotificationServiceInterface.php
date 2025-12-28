<?php

namespace App\Interfaces;

interface NotificationServiceInterface {
    public function sendNotification(string $email, string $name, string $subscriptionType, string $message): void;
}
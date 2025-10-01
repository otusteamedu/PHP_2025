<?php

namespace App\Services;

use App\Interfaces\NotificationServiceInterface;
use App\Interfaces\MailServiceInterface;
use App\Interfaces\LoggerInterface;

class NotificationService implements NotificationServiceInterface {
    private $mailService;
    private $logger;
    
    public function __construct(MailServiceInterface $mailService, LoggerInterface $logger) {
        $this->mailService = $mailService;
        $this->logger = $logger;
    }
    
    public function sendNotification(string $email, string $name, string $subscriptionType, string $message): void {
        if ($subscriptionType == 'premium') {
            echo "SMS to " . $name . ": " . $message . "\n";
        }
        
        $this->mailService->sendNotificationEmail($email, $message);
        $this->logger->log("Notification sent: " . $message);
    }
}
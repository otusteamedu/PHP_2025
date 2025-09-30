<?php

namespace App\Services;

use App\Interfaces\UserRepositoryInterface;
use App\Interfaces\LoggerInterface;
use App\Exceptions\InvalidSubscriptionTypeException;

class SubscriptionService {
    private $userRepository;
    private $logger;
    private $validTypes = ['free', 'premium', 'vip'];
    
    public function __construct(UserRepositoryInterface $userRepository, LoggerInterface $logger) {
        $this->userRepository = $userRepository;
        $this->logger = $logger;
    }
    
    public function upgradeSubscription(int $userId, string $type): void {
        if (!in_array($type, $this->validTypes)) {
            throw new InvalidSubscriptionTypeException("Invalid subscription type: " . $type);
        }
        
        $this->userRepository->updateSubscription($userId, $type);
        $this->logger->log("Subscription upgraded for user ID " . $userId . " to " . $type);
    }
}
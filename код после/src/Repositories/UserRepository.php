<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Entities\User;

class UserRepository implements UserRepositoryInterface {
    private $db;
    
    public function __construct(\mysqli $db) {
        $this->db = $db;
    }
    
    public function save(User $user): User {
        $stmt = $this->db->prepare("INSERT INTO users (name, email, subscription) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $user->name, $user->email, $user->subscriptionType);
        $stmt->execute();
        $user->id = $stmt->insert_id;
        return $user;
    }
    
    public function updateSubscription(int $userId, string $subscriptionType): void {
        $stmt = $this->db->prepare("UPDATE users SET subscription = ? WHERE id = ?");
        $stmt->bind_param("si", $subscriptionType, $userId);
        $stmt->execute();
    }
}
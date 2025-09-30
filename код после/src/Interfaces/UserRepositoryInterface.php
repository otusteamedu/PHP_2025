<?php

namespace App\Interfaces;

use App\Entities\User;

interface UserRepositoryInterface {
    public function save(User $user): User;
    public function updateSubscription(int $userId, string $subscriptionType): void;
}
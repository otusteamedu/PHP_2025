<?php

namespace App\Interfaces;

use App\Models\Job;

interface JobRepositoryInterface
{
    public function create(array $data): Job;
    public function find(string $id): ?Job;
    public function updateStatus(string $id, string $status): bool;
}
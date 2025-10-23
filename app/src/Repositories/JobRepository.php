<?php

namespace App\Repositories;

use App\Interfaces\JobRepositoryInterface;
use App\Models\Job;

class JobRepository implements JobRepositoryInterface
{
    private \PDO $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function create(array $data): Job
    {
        $id = uniqid('job_', true);
        $job = new Job(
            id: $id,
            status: 'pending',
            data: $data,
            createdAt: date('Y-m-d H:i:s'),
            updatedAt: date('Y-m-d H:i:s')
        );

        $stmt = $this->db->prepare(
            "INSERT INTO jobs (id, status, data, created_at, updated_at) 
             VALUES (?, ?, ?, ?, ?)"
        );
        
        $stmt->execute([
            $job->id,
            $job->status,
            json_encode($job->data),
            $job->createdAt,
            $job->updatedAt
        ]);

        return $job;
    }

    public function find(string $id): ?Job
    {
        $stmt = $this->db->prepare("SELECT * FROM jobs WHERE id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return new Job(
            id: $data['id'],
            status: $data['status'],
            data: json_decode($data['data'], true),
            result: $data['result'],
            createdAt: $data['created_at'],
            updatedAt: $data['updated_at']
        );
    }

    public function updateStatus(string $id, string $status): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE jobs SET status = ?, updated_at = ? WHERE id = ?"
        );
        
        return $stmt->execute([
            $status,
            date('Y-m-d H:i:s'),
            $id
        ]);
    }
}
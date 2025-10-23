<?php

namespace App\Jobs;

use App\Interfaces\JobRepositoryInterface;

class ProcessRequestJob
{
    private string $jobId;
    private array $data;

    public function __construct(string $jobId, array $data)
    {
        $this->jobId = $jobId;
        $this->data = $data;
    }

    public function handle(JobRepositoryInterface $jobRepository): void
    {
        // Обновляем статус на "в процессе"
        $jobRepository->updateStatus($this->jobId, 'processing');

        try {
            sleep(rand(2, 5));
            
            $result = $this->processData($this->data);
            
            $this->saveResult($jobRepository, $result);
            
        } catch (\Exception $e) {
            $jobRepository->updateStatus($this->jobId, 'failed');
        }
    }

    private function processData(array $data): array
    {
        // Пример обработки данных
        return [
            'processed_data' => array_map('strtoupper', $data),
            'timestamp' => time(),
            'items_count' => count($data)
        ];
    }

    private function saveResult(JobRepositoryInterface $jobRepository, array $result): void
    {
        $jobRepository->updateStatus($this->jobId, 'completed');
    }
}
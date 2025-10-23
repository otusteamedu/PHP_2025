<?php

namespace App\Controllers;

use App\Interfaces\JobRepositoryInterface;
use App\Interfaces\QueueServiceInterface;
use App\Jobs\ProcessRequestJob;

/**
 * @OA\Info(title="Queue API", version="1.0")
 */
class JobController
{
    public function __construct(
        private JobRepositoryInterface $jobRepository,
        private QueueServiceInterface $queueService
    ) {}

    /**
     * @OA\Post(
     *     path="/api/jobs",
     *     summary="Создать новую задачу",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"data"},
     *             @OA\Property(property="data", type="object", example={"key": "value"})
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Задача создана",
     *         @OA\JsonContent(
     *             @OA\Property(property="job_id", type="string"),
     *             @OA\Property(property="status", type="string")
     *         )
     *     )
     * )
     */
    public function create($request)
    {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['data'])) {
            return $this->json(['error' => 'Missing data field'], 400);
        }

        // Создаем запись в БД
        $job = $this->jobRepository->create($data['data']);

        // Добавляем в очередь
        $this->queueService->push('default', [
            'job_class' => ProcessRequestJob::class,
            'job_id' => $job->id,
            'data' => $data['data']
        ]);

        return $this->json([
            'job_id' => $job->id,
            'status' => $job->status
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/jobs/{job_id}",
     *     summary="Получить статус задачи",
     *     @OA\Parameter(
     *         name="job_id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Информация о задаче",
     *         @OA\JsonContent(
     *             @OA\Property(property="job_id", type="string"),
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="result", type="object", nullable=true)
     *         )
     *     ),
     *     @OA\Response(response=404, description="Задача не найдена")
     * )
     */
    public function status(string $jobId)
    {
        $job = $this->jobRepository->find($jobId);

        if (!$job) {
            return $this->json(['error' => 'Job not found'], 404);
        }

        return $this->json([
            'job_id' => $job->id,
            'status' => $job->status,
            'result' => $job->result ? json_decode($job->result, true) : null
        ]);
    }

    private function json(array $data, int $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        return json_encode($data);
    }
}
<?php

declare(strict_types=1);

namespace Ak\Hw\Services;
use Ak\Hw\Domain\Messaging\QueueProducerInterface;

class UserReportService
{
    private QueueProducerInterface $queueProducer;

    public function __construct(QueueProducerInterface $producer){
        $this->queueProducer = $producer;
    }

    /**
     * @param int $userId
     * @param string $email
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     */
    public function generateReport(int $userId, string $email, string $dateFrom, string $dateTo): array
    {
        // 1. Find user by ID and email (using repository)
        // $user = $this->userRepository->findByIdAndEmail($userId, $email);
        // if (!$user) {
        //     throw new \Exception('User not found.');
        // }


        // 2. For now, just return the input data
        $this->queueProducer->publish(
            'report generation',
            array('user_id' => $userId,
                'email' => $email,
                'date_from' => $dateFrom,
                'date_to' => $dateTo
            )
        );
        return ['status' => 'Report generation has been queued.'];
    }
}

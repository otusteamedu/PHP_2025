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
        $this->queueProducer->publish(
            'report generation',
            array('user_id' => $userId,
                'email' => $email,
                'date_from' => $dateFrom,
                'date_to' => $dateTo
            )
        );
        return ['status' => 'Генерация отчета поставлена в очередь.'];
    }
}

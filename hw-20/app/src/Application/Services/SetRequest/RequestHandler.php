<?php

declare(strict_types=1);

namespace App\Application\Services\SetRequest;

use App\Domain\Request\RequestRepository;
use App\Infrastructure\Transport\Amqp\RequestProducer;
use Psr\Log\LoggerInterface;
use Throwable;

final class RequestHandler
{
    private const STATUS_QUEUED = 'queued';
    private const STATUS_FAILED = 'failed';

    public function __construct(
        private RequestRepository $repository,
        private RequestProducer $producer,
    ) {
    }

    public function handle(Query $query): Output
    {
        $requestId = $this->repository->create($query->data, self::STATUS_QUEUED);

        try {
            $this->producer->publish($requestId, $query->data);
        } catch (Throwable $exception) {
            $this->repository->updateStatus($requestId, self::STATUS_FAILED);

            throw $exception;
        }

        return new Output($requestId, self::STATUS_QUEUED);
    }
}

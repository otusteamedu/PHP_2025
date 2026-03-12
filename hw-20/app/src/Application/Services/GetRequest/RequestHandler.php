<?php

declare(strict_types=1);

namespace App\Application\Services\GetRequest;

use App\Domain\Request\RequestRepository;
use App\Infrastructure\Transport\Amqp\RequestProducer;
use Http\Discovery\NotFoundException;
use Psr\Log\LoggerInterface;
use Throwable;

final class RequestHandler
{
    public function __construct(
        private RequestRepository $repository,
    ) {
    }

    public function handle(Query $query): ?Output
    {
        $request = $this->repository->getRequest($query->requestId);

        if (!$request) {
            return null;
        }

        return new Output($request['id'], $request['status']);
    }
}

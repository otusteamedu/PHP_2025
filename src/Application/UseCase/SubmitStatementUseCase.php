<?php
declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\DTO\SubmitStatementDto;
use App\Domain\StatementRequest;
use App\Infrastructure\RabbitMq\Statement\StatementProducer;

class SubmitStatementUseCase
{
    public function __construct(
        private readonly StatementProducer $producer
    ) {}

    public function handle(SubmitStatementDto $dto): string
    {
        $entity = StatementRequest::create(
            email: $dto->email,
            dateFrom: $dto->dateFrom,
            dateTo: $dto->dateTo
        );

        $this->producer->enqueue($entity);

        return $entity->id;
    }
}

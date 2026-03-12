<?php

declare(strict_types=1);

namespace Queues\Application\UseCases;

use Queues\Domain\Entities\Statement;
use Queues\Domain\Enums\StatementStatus;
use Queues\Application\DTO\StatementRequestDTO;
use Queues\Application\DTO\StatementResponseDTO;
use Queues\Application\Interfaces\QueueInterface;
use Queues\Application\Interfaces\StatementRequestValidatorInterface;

class CreateStatementUseCase
{
    public function __construct(
        private readonly QueueInterface $queue,
        private readonly StatementRequestValidatorInterface $validator
    ) {
    }

    public function execute(StatementRequestDTO $dto): StatementResponseDTO
    {
        $this->validator->validate($dto);

        $statement = Statement::create(
            $dto->dateFrom,
            $dto->dateTo,
            $dto->email
        );

        $this->queue->enqueue($statement->toJson());

        return new StatementResponseDTO(
            $statement->id,
            StatementStatus::PENDING,
            'Запрос принят в обработку. Выписка будет отправлена на email.'
        );
    }
}

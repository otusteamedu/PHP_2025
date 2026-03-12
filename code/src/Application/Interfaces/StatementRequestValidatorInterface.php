<?php

declare(strict_types=1);

namespace Queues\Application\Interfaces;

use Queues\Application\DTO\StatementRequestDTO;

interface StatementRequestValidatorInterface
{
    public function validate(StatementRequestDTO $dto): void;
}

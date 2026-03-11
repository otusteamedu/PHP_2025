<?php
declare(strict_types=1);

namespace App\Domain\Task\ValueObject;

use App\Domain\Exception\ValidationException;
use Ramsey\Uuid\Uuid;

class TaskId
{
    public function __construct(
        private string $value
    ) {
        if (!Uuid::isValid($value)) {
            throw new ValidationException('Task id must be a valid UUID');
        }
    }

    public function toString(): string
    {
        return $this->value;
    }
}

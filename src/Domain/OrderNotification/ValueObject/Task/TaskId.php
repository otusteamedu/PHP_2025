<?php

declare(strict_types=1);

namespace App\Domain\OrderNotification\ValueObject\Task;

use App\Domain\OrderNotification\Exception\Input\TaskIdException;

class TaskId
{
    private int $value;

    public function __construct(int $value)
    {
        if ($value <= 0) {
            throw TaskIdException::lessThanZero();
        }

        $this->value = $value;
    }

    public function value(): int
    {
        return $this->value;
    }

    /**
     * @throws TaskIdException
     */
    public static function fromInt(int $id): self
    {
        return new self($id);
    }
}

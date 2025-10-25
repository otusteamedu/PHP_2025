<?php

declare(strict_types=1);

namespace Camal\AppDataMapperLocal\Domain\Entities;

use DateTime;

class TaskDateTime
{
    private DateTime $value;

    public function __construct(?string $value = null)
    {
        if (empty($value)) {
            $this->value = new DateTime();
        } else {
            $this->value = new DateTime($value);
        }
    }

    public function toString(): string
    {
        if ($this->value === null) {
            return "";
        }
        return $this->value->format("Y-m-d h:i:s");
    }

    public function toStringShort(): string
    {
        if ($this->value === null) {
            return "";
        }
        return $this->value->format("Y-m-d");
    }
    
    public function getValue(): DateTime
    {
        return $this->value;
    }
}

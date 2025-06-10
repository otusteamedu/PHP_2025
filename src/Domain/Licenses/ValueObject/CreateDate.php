<?php
declare(strict_types=1);

namespace Domain\Licenses\ValueObject;

use DateTime;

class CreateDate
{
    private DateTime $value;

    public function __construct(DateTime $value)
    {
        $this->assertValidName($value);
        $this->value = $value;
    }

    public function getValue(): DateTime
    {
        return $this->value;
    }

    private function assertValidName(DateTime $value): void
    {
        if (false) {
            throw new \InvalidArgumentException('Error Licenses CreateDate');
        }
    }
}

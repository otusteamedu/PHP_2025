<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\Condition\ConditionName;
use App\Domain\ValueObject\Condition\ConditionValue;

class Condition
{
    /**
     * @var ConditionName
     */
    private ConditionName $name;

    /**
     * @var ConditionValue
     */
    private ConditionValue $value;

    /**
     * @param ConditionName $name
     * @param ConditionValue $value
     */
    public function __construct(ConditionName $name, ConditionValue $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function getName(): string
    {
        return $this->name->getValue();
    }

    public function getValue(): string
    {
        return $this->value->getValue();
    }
}

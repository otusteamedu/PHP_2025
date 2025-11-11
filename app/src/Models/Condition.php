<?php

declare(strict_types=1);

namespace App\Models;

use InvalidArgumentException;

/**
 * Class Condition
 * @package App\Models
 */
class Condition
{
    /**
     * @var string
     */
    private string $name;
    /**
     * @var string
     */
    private string $value;

    /**
     * @param string $name
     * @param string $value
     */
    public function __construct(string $name, string $value)
    {
        if (!$name) {
            throw new InvalidArgumentException('Condition name must not be empty.');
        }

        $this->name = $name;
        $this->value = $value;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'value' => $this->value,
        ];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}

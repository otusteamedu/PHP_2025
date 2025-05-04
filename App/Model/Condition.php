<?php

declare(strict_types=1);

namespace App\Model;

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
        if (empty($name)) {
            throw new \InvalidArgumentException('Имя условия не должно быть пустым.');
        }

        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return [$this->getName() => $this->getValue()];
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

    /**
     * @return string
     */
    public function getKeyForRedis(): string
    {
        return "conditions:{$this->getName()}:{$this->getValue()}";
    }
}

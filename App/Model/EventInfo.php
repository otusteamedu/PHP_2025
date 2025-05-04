<?php

declare(strict_types=1);

namespace App\Model;

class EventInfo
{
    /**
     * @var int
     */
    protected int $id;
    /**
     * @var string
     */
    protected string $name;

    /**
     * @param int $id
     * @param string $name
     */
    function __construct(int $id, string $name)
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException('$id должно быть больше 0');
        }

        $this->id = $id;
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array{id: int, name: string}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
        ];
    }
}

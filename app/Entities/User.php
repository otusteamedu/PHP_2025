<?php

namespace App\Entities;

class User extends Entity
{
    /** @var int|null */
    public ?int $id;

    /** @var string */
    public string $name;

    /**
     * @param array|null $data
     */
    public function __construct(?array $data = []) {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'];
    }
}
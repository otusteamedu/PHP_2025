<?php

namespace App\Entities;

class User extends Entity
{
    private string $name;

    public function __construct(?int $id, string $name) {
        $this->id = $id;
        $this->name = $name;
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name) {
        $this->name = $name;
    }
}
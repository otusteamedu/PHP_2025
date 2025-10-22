<?php

namespace Blarkinov\PhpDbCourse\Models\User;

use JsonSerializable;

class User implements JsonSerializable
{

    public function __construct(
        private int $id,
        private string $firstName,
        private string $lastName,
        private string $dateBirth,
        private bool $gender,
    ) {}

    public function getID(): int
    {
        return $this->id;
    }
    public function getFirstName(): string
    {
        return $this->firstName;
    }
    public function getLastName(): string
    {
        return $this->lastName;
    }
    public function getDateBirth(): string
    {
        return $this->dateBirth;
    }
    public function getGender(): int
    {
        return $this->gender;
    }

    public function jsonSerialize():mixed
    {
        return [
            'id' => $this->id,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'date_birth' => $this->dateBirth,
            'gender' => $this->gender,
        ];
    }
}

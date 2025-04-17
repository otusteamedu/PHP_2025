<?php

declare(strict_types=1);

namespace App\Domain\ValueObject\Teacher;

final class Gender
{
    public function __construct(private string $gender)
    {
        $this->assertGenderIsValid($gender);
    }

    public function getGender():string
    {
        return $this->gender;
    }

    private function assertGenderIsValid(string $value):void
    {
        $arGenders = ['MALE', 'FEMALE'];
        if (!in_array($value, $arGenders)) {
            throw new \InvalidArgumentException('Gender is invalid');
        }
    }

}

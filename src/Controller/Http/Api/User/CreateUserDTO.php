<?php

declare(strict_types=1);

namespace App\Controller\Http\Api\User;

readonly class CreateUserDTO
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        public \DateTimeImmutable $birthDate,
    ) {
    }

    public static function fromArray(array $data): self
    {
        $data = $data['createUser'] ?? throw new \InvalidArgumentException('Неверное тело запроса.', 400);

        $fields = array_keys(get_class_vars(self::class));
        if (!empty(array_diff($fields, array_keys($data)))) {
            throw new \InvalidArgumentException('Неверное тело запроса.', 400);
        }

        return new self(
            firstName: $data['firstName'],
            lastName: $data['lastName'],
            email: $data['email'],
            birthDate: \DateTimeImmutable::createFromFormat('Y-m-d', $data['birthDate']),
        );
    }
}

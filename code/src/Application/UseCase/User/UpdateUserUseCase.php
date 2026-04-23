<?php

declare(strict_types=1);

namespace App\Application\UseCase\User;

use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Entity\User;

readonly class UpdateUserUseCase
{
    public function __construct(private UserRepositoryInterface $userRepository)
    {
    }

    public function execute(int $userId, array $data): User
    {
        $user = $this->userRepository->findById($userId);
        if (isset($data['name'])) {
            $user->setName($data['name']);
        }
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }
        if (isset($data['born'])) {
            $user->setBorn(new \DateTimeImmutable($data['born']));
        }
        if (isset($data['telegram_id'])) {
            $user->setTelegramId((int)$data['telegram_id']);
        }
        if (isset($data['gender'])) {
            $user->setGender($data['gender']);
        }
        if (isset($data['weight'])) {
            $user->setWeight((int)$data['weight']);
        }
        if (isset($data['height'])) {
            $user->setHeight((int)$data['height']);
        }

        return $this->userRepository->save($user);
    }
}

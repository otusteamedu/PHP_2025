<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\User;
use App\Domain\Exception\UserNotFoundException;
use App\Repository\UserRepositoryInterface;

final class UpdateUserUseCase
{
    public function __construct(private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function execute(int $id, array $data): User
    {
        $user = $this->userRepository->findById($id);

        if ($user === null) {
            throw new UserNotFoundException($id);
        }

        if (isset($data['name'])) {
            $user->setName($data['name']);
        }
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
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
        if (isset($data['born'])) {
            $user->setBorn(new \DateTimeImmutable($data['born']));
        }

        return $this->userRepository->save($user);
    }
}

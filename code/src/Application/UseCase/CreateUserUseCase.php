<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\User;
use App\Domain\Exception\UserAlreadyExistsException;
use App\Repository\UserRepositoryInterface;

class CreateUserUseCase
{
    public function __construct(private readonly UserRepositoryInterface $userRepository)
    {
    }

    /**
     * @throws UserAlreadyExistsException
     * @throws \Exception
     */
    public function execute(array $data): User
    {
        // 1. Бизнес-валидация: проверяем, не занят ли email
        if ($this->userRepository->findByEmail($data['email'])) {
            throw new UserAlreadyExistsException('User with this email already exists.');
        }

        // 2. Создаем доменную сущность
        $user = new User(
            null, // ID будет присвоен базой данных
            $data['name'],
            $data['email'],
            $data['telegram_id'] ?? null,
            $data['gender'],
            (int)$data['weight'],
            (int)$data['height'],
            new \DateTimeImmutable($data['born'])
        );

        // 3. Сохраняем через репозиторий
        return $this->userRepository->save($user);
    }
}

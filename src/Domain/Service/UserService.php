<?php

namespace App\Domain\Service;

use App\Domain\Entity\User;
use App\Domain\Model\CreateUserModel;
use App\Domain\Model\UpdateUserEmailModel;
use App\Infrastructure\Database\Repository\UserRepository;

class UserService
{
    private readonly UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function createUser(CreateUserModel $createUserModel): User
    {
        $user = new User(
            firstName: $createUserModel->getFirstName(),
            lastName: $createUserModel->getLastName(),
            email: $createUserModel->getEmail(),
            birthDate: $createUserModel->getBirthDate(),
        );

        return $this->userRepository->save($user);
    }

    public function findUser(int $id): User
    {
        $user = $this->userRepository->find($id);
        if ($user === null) {
            throw new \Exception('Пользователь не найден.', 404);
        }

        return $user;
    }

    public function findUsers(): array
    {
        return $this->userRepository->findAll()->toArray();
    }

    public function updateUserEmail(UpdateUserEmailModel $updateUserModel): User
    {
        $user = $this
            ->findUser($updateUserModel->getUserId())
            ->setEmail($updateUserModel->getNewEmail());

        return $this->userRepository->save($user);
    }

    public function deleteUser(int $id): bool
    {
        $user = $this->findUser($id);

        return $this->userRepository->delete($user);
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\UserManagement;

use App\Domain\UserManagement\Collection\UserCollection;
use App\Domain\UserManagement\Entity\User;
use App\Domain\UserManagement\Exception\UserNotFoundException;
use App\Domain\UserManagement\Model\CreateUserModel;
use App\Domain\UserManagement\Model\GetUsersModel;
use App\Domain\UserManagement\Model\UpdateUserEmailModel;
use App\Infrastructure\Database\Repository\UserRepository;

class UserService
{
    public function __construct(
        private readonly UserRepository $userRepository,
    ) {
    }

    public function createUser(CreateUserModel $createUserModel): User
    {
        $user = new User(
            firstName: $createUserModel->getFirstName(),
            lastName: $createUserModel->getLastName(),
            email: $createUserModel->getEmail(),
            birthDate: $createUserModel->getBirthDate(),
        );

        $this->userRepository->save($user);

        return $user;
    }

    /**
     * @throws UserNotFoundException
     */
    public function findUser(int $id): User
    {
        $user = $this->userRepository->find($id);

        if ($user === null) {
            throw UserNotFoundException::byId($id);
        }

        return $user;
    }

    /**
     * @throws UserNotFoundException
     */
    public function findUsers(GetUsersModel $getUsersModel): UserCollection
    {
        return $this->userRepository->findAllPaginatedById(
            $getUsersModel->getLastId(),
            $getUsersModel->getLimit()
        );
    }

    /**
     * @throws UserNotFoundException
     */
    public function updateUserEmail(UpdateUserEmailModel $updateUserModel): User
    {
        $user = $this
            ->findUser($updateUserModel->getUserId())
            ->setEmail($updateUserModel->getNewEmail())
        ;

        $this->userRepository->save($user);

        return $user;
    }

    /**
     * @throws UserNotFoundException
     */
    public function deleteUser(int $id): void
    {
        $user = $this->findUser($id);

        $this->userRepository->delete($user);
    }
}

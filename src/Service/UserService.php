<?php declare(strict_types=1);

namespace App\Service;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Repository\UserRepository;

class UserService
{
    public function __construct(private UserRepository $userRepository) {}

    public function findById(int $id): UserDTO {
        $createdUser = $this->userRepository->find($id);
        return UserDTO::createFromEntity($createdUser);
    }

    public function create(string $name, string $email): UserDTO
    {
        $user = new User();
        $user->setName($name);
        $user->setEmail($email);

        $this->userRepository->save($user);
        $createdUser = $this->userRepository->find($user->getId());
        return UserDTO::createFromEntity($createdUser);
    }
}

<?php declare(strict_types=1);

namespace App\Service;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Repository\UserRepository;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function findById(int $id): UserDTO {
        $createdUser = $this->userRepository->findById($id);
        return UserDTO::createFromEntity($createdUser);
    }

    public function create(string $name, string $email): UserDTO
    {
        $user = $this->userRepository->save(
            new User($name, $email)
        );

        return UserDTO::createFromEntity($user);
    }
}

<?php
declare(strict_types=1);


namespace App\Application\DTO\User;

use App\Application\DTO\UserPost\UserPostDTOTransformer;
use App\Domain\Aggregate\User\User;

class UserDTOTransformer
{
    private UserPostDTOTransformer $userPostDTOTransformer;

    public function __construct()
    {
        $this->userPostDTOTransformer = new UserPostDTOTransformer();
    }

    public function fromEntity(User $user): UserDTO
    {
        $userDTO = new UserDTO();
        $userDTO->id = $user->id;
        $userDTO->name = $user->name;
        $userDTO->email = $user->email;
        $userDTO->posts = [];
        foreach ($user->getPosts() ?? [] as $post) {
            $userDTO->posts[] = $this->userPostDTOTransformer->fromEntity($post);
        }

        return $userDTO;
    }

    /**
     * @param array<User> $users
     *
     * @return array<UserDTO>
     */
    public function fromEntityList(array $users): array
    {
        $userDTOs = [];
        foreach ($users as $user) {
            $userDTOs[] = $this->fromEntity($user);
        }

        return $userDTOs;
    }

}
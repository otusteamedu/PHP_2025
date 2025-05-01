<?php
declare(strict_types=1);


namespace App\User\Application\DTO\User;

use App\User\Domain\Aggregate\User\User;

class UserDTOTransformer
{

    public function fromEntity(User $user): UserDTO
    {
        $userDTO = new UserDTO();
        $userDTO->id = $user->id;
        $userDTO->name = $user->name;
        $userDTO->email = $user->email;

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
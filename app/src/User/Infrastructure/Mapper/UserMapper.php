<?php
declare(strict_types=1);


namespace App\User\Infrastructure\Mapper;

use App\User\Domain\Aggregate\User\User;
use App\User\Domain\Factory\UserFactory;

class UserMapper
{
    private UserFactory $factory;

    public function __construct()
    {
        $this->factory = new UserFactory();
    }

    public function userMap(array $row): User
    {
        $user = $this->factory->create($row['email'], $row['name']);
        $reflection = new \ReflectionClass($user);
        $property = $reflection->getProperty('id');
        $property->setValue($user, $row['id']);

        return $user;

    }

}
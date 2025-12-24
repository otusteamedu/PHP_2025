<?php
declare(strict_types=1);


namespace App\Infrastructure\Mapper;

use App\Domain\Aggregate\User\User;
use App\Domain\Aggregate\User\UserPost;
use App\Domain\Factory\UserPostFactory;

class UserPostMapper
{
    private UserPostFactory $factory;

    public function __construct()
    {
        $this->factory = new UserPostFactory();
    }

    public function userPostMap(User $user, array $row): UserPost
    {
        $post = $this->factory->create($user, $row['title'], $row['content']);
        $reflection = new \ReflectionClass($post);
        $property = $reflection->getProperty('id');
        $property->setValue($post, $row['id']);

        return $post;
    }

}
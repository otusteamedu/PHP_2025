<?php
declare(strict_types=1);


namespace App\Domain\Factory;

use App\Domain\Aggregate\User\User;
use App\Domain\Aggregate\User\UserPost;

class UserPostFactory
{
    public function create(User $user, $title, string $content): UserPost
    {
        return new UserPost($title, $content, $user);
    }

}
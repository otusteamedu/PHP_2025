<?php
declare(strict_types=1);


namespace App\Application\DTO\UserPost;

class UserPostDTO
{
    public ?string $id;
    public ?string $title;
    public ?string $content;
    public ?string $owner_id;
}
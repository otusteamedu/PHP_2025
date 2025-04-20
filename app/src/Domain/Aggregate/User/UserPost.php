<?php
declare(strict_types=1);


namespace App\Domain\Aggregate\User;

use App\Domain\Service\UuidService;

class UserPost
{
    public string $id {
        get {
            return $this->id;
        }
    }

    public function __construct(
        private string        $title,
        private string        $content,
        private readonly User $owner,
    )
    {
        $this->id = UuidService::generate();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

}
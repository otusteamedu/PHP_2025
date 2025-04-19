<?php
declare(strict_types=1);


namespace App\Domain\Aggregate\User;

use App\Domain\Service\AssertService;
use App\Domain\Service\UuidService;

class User
{

    private ?array $posts = null;
    public string $id {
        get {
            return $this->id;
        }
    }

    public function __construct(
        public string    $email {
            get {
                return $this->email;
            }
            set(string $email) {
                $this->email = $email;
                AssertService::email($this->email);
                AssertService::lengthBetween($this->email, 10, 255);
            }
        }, public string $name {
        get {
            return $this->name;
        }
        set(string $name) {
            $this->name = $name;
            AssertService::lengthBetween($this->name, 1, 255);
        }
    }
    )
    {
        $this->id = UuidService::generate();
    }

    public function getPosts(): ?array
    {
        return $this->posts;
    }

    public function addPost(UserPost $post): void
    {
        if (!$this->posts) {
            $this->posts = [];
        }
        if (in_array($post, $this->posts, true)) {
            return;
        }
        $this->posts[] = $post;
    }

}
<?php
declare(strict_types=1);


namespace App\User\Domain\Aggregate\User;

use App\Shared\Domain\Service\AssertService;
use App\Shared\Domain\Service\UuidService;

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

    public function getPosts(): array
    {
        if (!isset($this->posts)) {
            $this->posts = isset($this->postRef) ? ($this->postRef)($this) : [];
        }
        return $this->posts;
    }

}
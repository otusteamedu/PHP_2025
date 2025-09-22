<?php
declare(strict_types=1);

namespace App\Users;

use App\Posts\LazyPostsCollection;

final readonly class User
{
    private function __construct(
        public int $id,
        public string $name,
        public string $email,
        private ?LazyPostsCollection $posts = null,
    ) {}

    public static function new(string $name, string $email): self
    {
        return new self(0, $name, $email, null);
    }

    public static function fromArray(array $row): self
    {
        return new self((int) $row['id'], (string) $row['name'], (string) $row['email'], null);
    }

    public function withId(int $id): self
    {
        return new self($id, $this->name, $this->email, $this->posts);
    }

    public function withPosts(LazyPostsCollection $posts): self
    {
        return new self($this->id, $this->name, $this->email, $posts);
    }

    public function posts(): LazyPostsCollection
    {
        return $this->posts ?? new LazyPostsCollection(fn() => []);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
        ];
    }
}

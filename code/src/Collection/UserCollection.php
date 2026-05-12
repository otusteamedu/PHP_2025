<?php

namespace App\Collection;

use App\Entity\User;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * Коллекция пользователей для результата массового получения из DataMapper.
 */
class UserCollection implements IteratorAggregate, Countable
{
    /**
     * @var User[]
     */
    private array $users = [];

    public function add(User $user): void
    {
        $this->users[] = $user;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->users);
    }

    public function count(): int
    {
        return count($this->users);
    }
}

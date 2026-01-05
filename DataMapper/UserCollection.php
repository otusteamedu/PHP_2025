<?php
declare(strict_types=1);

class UserCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var User[]
     */
    private array $items = [];

    /**
     * @param User[] $users
     */
    public function __construct(array $users = [])
    {
        foreach ($users as $user) {
            $this->add($user);
        }
    }

    /**
     * @param User $user
     * @return void
     */
    public function add(User $user): void
    {
        $this->items[] = $user;
    }

    /**
     * @return Traversable
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return User[]
     */
    public function toArray(): array
    {
        return $this->items;
    }
}

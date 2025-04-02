<?php

declare(strict_types=1);

namespace App\Collections;

use App\Entities\Team;
use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * Class TeamsCollection
 * @package App\Collections
 */
class TeamsCollection implements IteratorAggregate
{
    /**
     * @var Team[]
     */
    private array $list = [];

    /**
     * @param Team $team
     * @return void
     */
    public function add(Team $team): void
    {
        $this->list[] = $team;
    }

    /**
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->list);
    }
}

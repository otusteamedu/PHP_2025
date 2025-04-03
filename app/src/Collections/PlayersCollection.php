<?php

declare(strict_types=1);

namespace App\Collections;

use App\Entities\Player;
use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * Class PlayersCollection
 * @package App\Collections
 */
class PlayersCollection implements IteratorAggregate
{
    /**
     * @var Player[]
     */
    private array $list = [];

    /**
     * @param Player $player
     * @return void
     */
    public function add(Player $player): void
    {
        $this->list[] = $player;
    }

    /**
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->list);
    }
}

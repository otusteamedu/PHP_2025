<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Collections;

use ArrayIterator;
use IteratorAggregate;
use Traversable;
use Zibrov\OtusPhp2025\Entities\Category;

class CategoryCollection implements IteratorAggregate
{

    private array $list = [];

    public function add(Category $team): void
    {
        $this->list[] = $team;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->list);
    }
}

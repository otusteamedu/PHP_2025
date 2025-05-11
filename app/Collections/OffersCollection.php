<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Collections;

use ArrayIterator;
use IteratorAggregate;
use Traversable;
use Zibrov\OtusPhp2025\Entities\Offers;

class OffersCollection implements IteratorAggregate
{

    private array $list = [];

    public function add(Offers $offers): void
    {
        $this->list[] = $offers;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->list);
    }
}

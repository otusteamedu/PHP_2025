<?php
declare(strict_types=1);

namespace App\Posts;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

final class LazyPostsCollection implements IteratorAggregate, Countable
{
    /**
     * @var callable(): Post[]
     */
    private $loader;

    /** @var null|Post[] */
    private ?array $cache = null;

    /**
     * @param callable(): Post[] $loader
     */
    public function __construct(callable $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @return Post[]
     */
    public function all(): array
    {
        if ($this->cache === null) {
            $loaded = ($this->loader)();
            $this->cache = array_values($loaded);
        }

        return $this->cache;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->all());
    }

    public function count(): int
    {
        return count($this->all());
    }
}

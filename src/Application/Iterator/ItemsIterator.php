<?

declare(strict_types=1);

namespace Kamalo\BurgersShop\Application\Iterator;

use Kamalo\BurgersShop\Domain\Iterator\ItemsIteratorInterface;
use Kamalo\BurgersShop\Domain\ValueObject\Item;

class ItemsIterator implements ItemsIteratorInterface
{
    public function __construct(
        private array $items = [],
        private int $position = 0
    ) {
    }

    public function hasNext(): bool
    {
        return $this->position < count($this->items);
    }

    public function next(): Item
    {
        return $this->items[$this->position++];
    }

    public function add(Item $item): void
    {
        $this->items[$this->position++] = $item;
    }
}
<?

declare(strict_types=1);

namespace Kamalo\BurgersShop\Domain\Iterator;

use Kamalo\BurgersShop\Domain\ValueObject\Item;

interface ItemsIteratorInterface
{
    public function hasNext(): bool;

    public function next(): Item;

    public function add(Item $item): void;
}
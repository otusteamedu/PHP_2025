<?

declare(strict_types=1);

namespace Kamalo\BurgersShop\Domain\Builder;

use Kamalo\BurgersShop\Domain\Entity\Product;
use Kamalo\BurgersShop\Domain\Iterator\ItemsIteratorInterface;

interface ProductBuilderInterface
{
    public function setProductCode(string $productCode): void;

    public function setItem(string $itemCode, int $count): void;

    public function setExtraItem(string $itemCode, int $count): void;

    public function getName(): string;

    public function getPrice(): int;

    public function getItemsIterator(): ItemsIteratorInterface;

    public function getExtraItemsIterator(): ItemsIteratorInterface;

    public function build(): Product;
}
<?

declare(strict_types=1);

namespace Kamalo\BurgersShop\Domain\Collection;

use Kamalo\BurgersShop\Domain\Entity\Product;

class ProductsCollection{
    private array $items;

    public function add(Product $product): void{
        $this->items[] = $product;
    }
}
<?

declare(strict_types=1);

namespace Kamalo\BurgersShop\Domain\Entity;

use Kamalo\BurgersShop\Domain\Builder\ProductBuilderInterface;
use Kamalo\BurgersShop\Domain\Iterator\ItemsIteratorInterface;

class Hotdog extends Product
{
    private ?int $id;

     /**
     * ID продукта
     * @var string
     */
    private string $productCode;

    /**
     * Наименование в меню
     * @var string
     */
    private string $name;

    /**
     * Цена продукта в меню
     * @var int
     */
    private int $price;

    /**
     * Итератор ингредиентов
     * @var ItemsIteratorInterface
     */
    private ItemsIteratorInterface $itemsIterator;

    /**
     * Итератор ингредиентов
     * @var ItemsIteratorInterface
     */
    private ItemsIteratorInterface $extraItemsIterator;

    public function __construct(
        ProductBuilderInterface $builder
    ) {
        $this->name = $builder->getName();
        $this->price = $builder->getPrice();
        $this->itemsIterator = $builder->getItemsIterator();
        $this->extraItemsIterator = $builder->getExtraItemsIterator();
    }
}
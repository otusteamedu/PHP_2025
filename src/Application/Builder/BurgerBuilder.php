<?

declare(strict_types=1);

namespace Kamalo\BurgersShop\Application\Builder;

use Exception;
use Kamalo\BurgersShop\Application\Enviropment\ConfigInterface;
use Kamalo\BurgersShop\Application\Iterator\ItemsIterator;
use Kamalo\BurgersShop\Domain\Builder\ProductBuilderInterface;
use Kamalo\BurgersShop\Domain\Entity\Burger;
use Kamalo\BurgersShop\Domain\Iterator\ItemsIteratorInterface;
use Kamalo\BurgersShop\Domain\ValueObject\Item;

class BurgerBuilder implements ProductBuilderInterface
{
    private string $productCode;

    private string $name;

    private int $price;

    private int $extraPrice;

    public function __construct(
        private ConfigInterface $config,
        private ItemsIteratorInterface $itemsIterator,
        private ItemsIteratorInterface $extraItemsIterator
    ) {
        $this->extraPrice = 0;
    }

    public function setProductCode(string $productCode): void
    {
        $this->productCode = $productCode;
    }
    public function setItem(string $itemCode, int $count): void
    {
        $this->itemsIterator->add(
            new Item(
                $itemCode,
                $count,
                0
            )
        );
    }

    public function setExtraItem(string $itemCode, int $count): void
    {
        if (!$price = $this->config->get('extras', $itemCode)) {
            throw new Exception('Некорретный код продукта: ' . $itemCode);
        }

        $this->extraPrice += $price;

        $this->extraItemsIterator->add(
            new Item(
                $itemCode,
                $count,
                $price
            )
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getItemsIterator(): ItemsIterator
    {
        return $this->itemsIterator;
    }

    public function getExtraItemsIterator(): ItemsIterator
    {
        return $this->extraItemsIterator;
    }

    public function build(): Burger
    {
        $burgerData = $this->config->get('burgers', $this->productCode);

        if (!$burgerData) {
            throw new Exception('отсутствует рецепт бургера: ' . $this->productCode);
        }

        $this->name = $burgerData['name'];
        $this->price = intval($burgerData['price']) + $this->extraPrice;

        foreach ($burgerData['ingredients'] as $code => $count) {
            $this->itemsIterator->add(
                new Item(
                    $code,
                    $count,
                    0
                )
            );
        }

        $burger = new Burger($this);

        $this->itemsIterator = new ItemsIterator();
        $this->extraItemsIterator = new ItemsIterator();

        return $burger;
    }
}
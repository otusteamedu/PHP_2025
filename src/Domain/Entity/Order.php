<?

declare(strict_types=1);

namespace Kamalo\BurgersShop\Domain\Entity;

use Exception;
use Kamalo\BurgersShop\Domain\Collection\ProductsCollection;

class Order
{
    private ?int $id;

    private int $status;

    private Int $price;

    private ProductsCollection $products;

    private array $statuses = [
        'created' => 1,
        'processing' => 2,
        'ready' => 3,
        'paid' => 4,
        'canceled' => 5
    ];

    public function __construct() {
        $this->status = $this->statuses['created'];
        $this->products = new ProductsCollection();

        $this->price = 0;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getProducts(): ProductsCollection
    {
        return $this->products;
    }

    public function changeStatus(string $statusCode)
    {
        if (!isset($this->statuses[$statusCode])) {
            throw new Exception('Некорретный статус заказа №' . $this->id);
        }

        $this->status = $this->statuses[$statusCode];

        return $this;
    }

    private function changePrice()
    {
        foreach ($this->products as $product) {
            $this->price += $product->getPrice();
        }
    }
}
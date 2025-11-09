<?

declare(strict_types=1);

namespace Kamalo\BurgersShop\Domain\ValueObject;

use Exception;

class Item
{
    private string $value;

    private int $count;

    private int $price;

    public function __construct(
        string $value,
        int $count,
        int $price
    ) {
        $this->validateValue($value);
        $this->validateCount($count);

        $this->value = $value;
        $this->count = $count;
        $this->price = $price;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    private function validateValue($value): void
    {
        if (empty($value)) {
            throw new Exception('Код продукта не может быть пустым');
        }
    }

    private function validateCount($count): void
    {
        if ($count <= 0) {
            throw new Exception('Количество не может быть пустым');
        }
    }

    private function validatePrice($count): void
    {
        if ($count < 0) {
            throw new Exception('Цена не может быть пустой');
        }
    }
}
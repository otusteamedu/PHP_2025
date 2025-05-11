<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Entities;

use Zibrov\OtusPhp2025\Proxy\CategoryProxy;

class Offers extends AbstractEntity
{
    private CategoryProxy $categoryProxy;

    public function __construct(
        ?int           $id,
        private int    $categoryId,
        private string $name,
        private string $color,
        private float  $price,
    )
    {
        parent::__construct($id);
        $this->categoryProxy = new CategoryProxy();
    }

    public function getAttributes(): array
    {
        return [
            'id' => $this->getId(),
            'category_id' => $this->getCategoryId(),
            'name' => $this->getName(),
            'color' => $this->getColor(),
            'price' => $this->getPrice(),
        ];
    }

    public static function create(array $data): static
    {
        return new Offers(
            $data['id'] ?? null,
            $data['category_id'],
            $data['name'],
            $data['color'],
            (float)$data['price'],
        );
    }

    public function getCategory(): ?Category
    {
        return $this->categoryProxy->getCategory($this);
    }

    public function getCategoryId(): int
    {
        return $this->categoryId;
    }

    public function setCategoryId(int $categoryId): void
    {
        $this->categoryId = $categoryId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }
}

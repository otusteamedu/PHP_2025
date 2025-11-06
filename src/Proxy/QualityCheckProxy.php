<?php

declare(strict_types=1);

namespace Restaurant\Proxy;

use Restaurant\Product\ProductInterface;

class QualityCheckProxy implements ProductInterface
{
    private bool $disposed = false;

    public function __construct(private readonly ProductInterface $product)
    {
    }

    public function getDescription(): string
    {
        return $this->product->getDescription();
    }

    public function getPrice(): float
    {
        return $this->product->getPrice();
    }

    public function cook(): void
    {
        echo "=== Начало процесса готовки ===\n";
        echo "Пре-событие: Проверка наличия ингредиентов\n";

        $this->product->cook();

        echo "Пост-событие: Проверка качества продукта\n";

        if (!$this->product->isQualityAcceptable()) {
            echo "ВНИМАНИЕ: Продукт не соответствует стандарту качества!\n";
            echo "Утилизируем продукт\n";
            $this->disposed = true;
        } else {
            echo "Продукт прошел проверку качества\n";
        }

        echo "=== Завершение процесса готовки ===\n";
    }

    public function isQualityAcceptable(): bool
    {
        return $this->product->isQualityAcceptable();
    }

    public function isDisposed(): bool
    {
        return $this->disposed;
    }
}

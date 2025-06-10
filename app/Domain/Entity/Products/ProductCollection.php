<?php
declare(strict_types=1);

namespace App\Domain\Entity\Products;

use App\Domain\Entity\Products\ProductInterface;

class ProductCollection
{

    private array $arProducts = [];

    public function addProduct($obj, int $key = 0)
    {
        if ($key === 0) {
            $this->arProducts[] = $obj;
        } else {
            if (isset($this->items[$key])) {
                throw new KeyHasUseException('Key $key already in use.');
            }
            $this->arProducts[$key] = $obj;
        }
    }

    public function deleteProduct(int $key)
    {
        if (!isset($this->arProducts[$key])) {
            throw new KeyInvalidException('Invalid key ' . $key);
        }

        unset($this->arProducts[$key]);
    }

    public function getProduct(int $key)
    {
        if (isset($this->arProducts[$key])) {
            return $this->arProducts[$key];
        }

        throw new KeyInvalidException("Invalid key $key.");
    }

    public function getProductCollection(): array
    {
        return $this->arProducts;
    }

    public function getListNameProduct(): array
    {
        $arName = [];
        foreach ($this->arProducts as $product) {
            /** @var ProductInterface $product */
            $arName[] = $product->getName();
        }

        return $arName;
    }
}
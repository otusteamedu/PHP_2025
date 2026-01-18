<?php

declare(strict_types=1);

namespace Otus\DataMapper\Factory;

use DateTime;
use Otus\DataMapper\Entity\Product;
use Throwable;

final class ProductFactory
{
    /**
     * @param array $data
     *
     * @return Product
     */
    public static function factory(array $data): Product
    {
        [
            'id' => $id,
            'brand' => $brand,
            'title' => $title,
            'price' => $price,
            'capacity' => $capacity,
            'hidden' => $hidden,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ] = $data;

        try {
            $createdAt = new DateTime($createdAt);
            $updatedAt = new DateTime($updatedAt);
        } catch (Throwable) {
            $createdAt = new DateTime();
            $updatedAt = new DateTime();
        }

        $product = new Product(
            $brand,
            $title,
            $price,
            $capacity,
            $hidden,
            $createdAt,
            $updatedAt,
        );

        $product->id = $id;

        return $product;
    }
}

<?php

declare(strict_types=1);

namespace Otus\DataMapper\Factory;

use Otus\DataMapper\Connection\Database;
use Otus\DataMapper\Entity\Product;
use Otus\DataMapper\Mapper\ProductMapper;
use Otus\DataMapper\Repository\ProductRepository;

final class RepositoryFactory
{
    /**
     * @param string $class
     *
     * @return mixed
     */
    public static function factory(string $class): mixed
    {
        return match ($class) {
            Product::class => (static function (): ProductRepository {
                $pdo = Database::getInstance()->getPdo();
                $mapper = new ProductMapper($pdo, 'products');

                return new ProductRepository($mapper);
            })()
        };
    }
}

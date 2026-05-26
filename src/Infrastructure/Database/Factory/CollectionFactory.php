<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\Factory;

use App\Domain\Shared\Collection\AbstractCollection;

class CollectionFactory
{
    /**
     * @param class-string<AbstractCollection> $collectionClass
     */
    public function create(string $collectionClass): AbstractCollection
    {
        if (!is_subclass_of($collectionClass, AbstractCollection::class)) {
            throw new \InvalidArgumentException(
                "Класс $collectionClass должен наследоваться от " . AbstractCollection::class
            );
        }

        return new $collectionClass();
    }
}

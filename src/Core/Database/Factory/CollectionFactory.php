<?php

declare(strict_types=1);

namespace App\Core\Database\Factory;

use App\Core\Database\Collection\AbstractCollection;

class CollectionFactory
{
    /**
     * @param class-string<AbstractCollection> $collectionClass
     */
    public function create(string $collectionClass): AbstractCollection
    {
        if (!is_subclass_of($collectionClass, AbstractCollection::class)) {
            throw new \InvalidArgumentException(sprintf(
                'Class %s must be subclass of %s',
                $collectionClass,
                AbstractCollection::class,
            ));
        }

        return new $collectionClass();
    }
}

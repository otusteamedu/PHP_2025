<?php

declare(strict_types=1);

namespace App\Domain\UserManagement\Collection;

use App\Domain\Shared\Collection\AbstractCollection;
use App\Domain\Shared\Entity\EntityInterface;
use App\Domain\UserManagement\Entity\User;

class UserCollection extends AbstractCollection
{
    public function add(EntityInterface $entity): void
    {
        if (!$entity instanceof User) {
            throw new \InvalidArgumentException(sprintf(
                '[%s] expects [%s] entity, got [%s]',
                UserCollection::class,
                User::class,
                get_class($entity),
            ));
        }

        $this->addEntity($entity);
    }
}

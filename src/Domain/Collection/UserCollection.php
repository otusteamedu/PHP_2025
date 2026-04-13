<?php

namespace App\Domain\Collection;

use App\Domain\Entity\EntityInterface;
use App\Domain\Entity\User;

class UserCollection extends AbstractCollection
{
    /**
     * @param User $entity
     */
    public function add(EntityInterface $entity): void
    {
        if (!$entity instanceof User) {
            throw new \RuntimeException('Ожидается объект класса ' . User::class);
        }

        $this->addEntity($entity);
    }
}

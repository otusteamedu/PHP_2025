<?php

declare(strict_types=1);

namespace App\Domain\UserManagement\Collection;

use App\Domain\Shared\Collection\AbstractCollection;
use App\Domain\Shared\Entity\EntityInterface;
use App\Domain\UserManagement\Entity\User;

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

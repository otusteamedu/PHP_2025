<?php

declare(strict_types=1);

namespace App\Domain\UserManagement\Collection;

use App\Core\Database\Collection\AbstractCollection;
use App\Core\Database\Entity\EntityInterface;
use App\Domain\UserManagement\Entity\User;

class UserCollection extends AbstractCollection
{
    public function add(EntityInterface $entity): void
    {
        if (!$entity instanceof User) {
            throw new \InvalidArgumentException(sprintf(
                'Ожидается сущность %s, получен %s',
                User::class,
                get_class($entity),
            ));
        }

        $this->addEntity($entity);
    }

    public function maxId(): ?int
    {
        $max = null;
        foreach ($this as $user) {
            if ($max === null || $user->getId() > $max) {
                $max = $user->getId();
            }
        }

        return $max;
    }
}

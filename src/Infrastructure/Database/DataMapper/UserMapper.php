<?php

declare(strict_types=1);

namespace App\Infrastructure\Database\DataMapper;

use App\Core\Database\DataMapper\AbstractMapper;
use App\Core\Database\DataMapper\DataMapperInterface;
use App\Domain\Shared\Entity\EntityInterface;
use App\Domain\UserManagement\Entity\User;

class UserMapper extends AbstractMapper implements DataMapperInterface
{
    public function mapRowToEntity(array $row): User
    {
        $user = new User(
            firstName: $row['first_name'],
            lastName: $row['last_name'],
            email: $row['email'],
            birthDate: new \DateTimeImmutable($row['birth_date']),
        );

        $this->hydrateId($user, $row['id']);

        return $user;
    }

    /**
     * @param User $entity
     */
    public function mapEntityToRow(EntityInterface $entity): array
    {
        if (!$entity instanceof User) {
            throw new \RuntimeException('Ожидается объект класса ' . User::class);
        }

        return [
            'id' => $entity->getId(),
            'first_name' => $entity->getFirstName(),
            'last_name' => $entity->getLastName(),
            'email' => $entity->getEmail(),
            'birth_date' => $entity->getBirthDate()->format('Y-m-d'),
        ];
    }
}

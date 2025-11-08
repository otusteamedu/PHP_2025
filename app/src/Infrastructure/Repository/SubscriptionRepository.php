<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Entity\Category;
use App\Domain\Entity\Subscription;
use App\Domain\Repository\SubscriptionRepositoryInterface;
use App\Domain\ValueObject\Email;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subscription>
 */
class SubscriptionRepository extends ServiceEntityRepository implements SubscriptionRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }

    public function findAllByCategory(Category $category): array
    {
        return $this->findBy([
            'category' => $category,
        ]);
    }

    public function findOneById(int $id): ?Subscription
    {
        return $this->find($id);
    }

    public function findOneByCategoryAndEmail(Category $category, Email $email): ?Subscription
    {
        return $this->findOneBy([
            'category' => $category,
            'email.value' => $email->getValue(),
        ]);
    }

    public function save(Subscription $subscription): void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($subscription);
        $entityManager->flush();
    }
}

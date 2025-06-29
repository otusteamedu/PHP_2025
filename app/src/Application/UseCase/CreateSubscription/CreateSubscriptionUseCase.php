<?php

declare(strict_types=1);

namespace App\Application\UseCase\CreateSubscription;

use App\Domain\Entity\Subscription;
use App\Domain\Repository\CategoryRepositoryInterface;
use App\Domain\Repository\SubscriptionRepositoryInterface;
use App\Domain\ValueObject\Email;
use RuntimeException;

readonly class CreateSubscriptionUseCase
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private SubscriptionRepositoryInterface $subscriptionRepository,
    )
    {
    }

    public function __invoke(CreateSubscriptionRequest $request): CreateSubscriptionResponse
    {
        $category = $this->categoryRepository->findOneByName($request->category);
        if ($category === null) {
            throw new RuntimeException(
                "Category «{$request->category}» does not exist"
            );
        }

        $subscription = $this->subscriptionRepository->findOneByCategoryAndEmail($category, new Email($request->email));
        if ($subscription !== null) {
            throw new RuntimeException(
                "Subscription «{$request->email}» to category «{$request->category}» already exists"
            );
        }

        $subscription = new Subscription($category, new Email($request->email));

        $this->subscriptionRepository->save($subscription);

        return new CreateSubscriptionResponse("You have successfully subscribed to the category «{$request->category}».");
    }
}

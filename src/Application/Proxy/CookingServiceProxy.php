<?php

namespace App\Application\Proxy;

use App\Application\Observer\CookingStatusSubject;
use App\Domain\Entity\Interface\ProductInterface;
use App\Domain\Exception\QualityCheckFailedException;
use App\Domain\Cooking\CookingStatus;

class CookingServiceProxy implements CookingServiceInterface
{
    private CookingServiceInterface $cookingService;

    private CookingStatusSubject $subject;

    public function __construct(
        CookingServiceInterface $cookingService,
        CookingStatusSubject $subject
    ) {
        $this->cookingService = $cookingService;
        $this->subject = $subject;
    }

    /**
     * @throws QualityCheckFailedException
     */
    public function cook(ProductInterface $product): void
    {
        $this->preEvent($product);

        $this->cookingService->cook($product);

        $this->postEvent($product);
    }

    private function preEvent(ProductInterface $product): void
    {
        $ingredients = $product->getIngredients();
        if (empty($ingredients)) {
            throw new \RuntimeException("No ingredients found!");
        }
    }

    /**
     * @throws QualityCheckFailedException
     */
    private function postEvent(ProductInterface $product): void
    {
        if (!$product->isMeetsStandard()) {
            $this->subject->changeStatus($product, CookingStatus::CANCELED);
            throw new QualityCheckFailedException("Quality check failed!");
        }
    }
}

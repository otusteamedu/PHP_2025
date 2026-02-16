<?php

namespace App\Application\Proxy;

use App\Application\Observer\CookingStatusSubject;
use App\Domain\Entity\Interface\ProductInterface;
use App\Domain\Cooking\CookingStatus;

class CookingService implements CookingServiceInterface
{
    private CookingStatusSubject $subject;

    public function __construct(CookingStatusSubject $subject)
    {
        $this->subject = $subject;
    }

    public function cook(ProductInterface $product): void
    {
        $this->subject->changeStatus($product, CookingStatus::COOKING);

        sleep(1);

        $this->subject->changeStatus($product, CookingStatus::COMPLETED);
    }
}

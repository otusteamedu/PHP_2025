<?php
declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Entity\Process\CookingProcessPresenter;
use App\Domain\Entity\Process\CookingProcessRepository;
use App\Domain\Entity\Products\ProductInterface;

class TrackCookingProcessUseCase
{

    public function __construct(
        private readonly ProductInterface $product
    )
    {
    }

    public function __invoke(): void
    {
        $cookingProcessRepository = new CookingProcessRepository();
        $cookingProcessPresenter = new CookingProcessPresenter();

        $cookingProcess = $cookingProcessRepository->createNewProduct($this->product->getName());

        $cookingProcessPresenter->messageBeforeCreateProduct($cookingProcess);
        $cookingProcessPresenter->messageAfterCreateProduct($cookingProcess);
    }
}
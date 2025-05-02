<?php
declare(strict_types=1);


namespace App\Food\Application\UseCase\MakeBurger;

use App\Food\Domain\Factory\BurgerFactory;
use App\Food\Domain\Service\FoodOrganizer\FoodOrganizer;

readonly class MakeBurgerUseCase
{
    public function __construct(
        private BurgerFactory $factory,
        private FoodOrganizer $organizer,
    )
    {
    }

    public function __invoke(MakeBurgerRequest $request): MakeBurgerResponse
    {
        $burger = $this->factory->build($request->title);
        $burger = $this->organizer->make($burger);
        //todo сохранить в бд

        return new MakeBurgerResponse($burger->getId());
    }

}
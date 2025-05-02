<?php
declare(strict_types=1);


namespace App\Food\Application\UseCase\MakeBurger;

use App\Food\Domain\Factory\BurgerFactory;
use App\Food\Domain\Factory\FoodFactoryInterface;

class MakeBurgerUseCase
{
    public function __construct(private readonly BurgerFactory $factory)
    {
    }

    public function __invoke(MakeBurgerRequest $request): MakeBurgerResponse
    {
        $burger = $this->factory->make($request->title);

        return new MakeBurgerResponse($burger->getId());
    }

}
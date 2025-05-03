<?php
declare(strict_types=1);


namespace App\Food\Application\UseCase\AddBurger;

use App\Food\Domain\Factory\BurgerFactory;
use App\Food\Domain\Repository\FoodOrderRepositoryInterface;
use App\Food\Domain\Repository\FoodRepositoryInterface;
use App\Food\Domain\Service\FoodOrganizer\FoodOrganizer;
use Webmozart\Assert\Assert;

readonly class AddBurgerUseCase
{
    public function __construct(
        private BurgerFactory                $factory,
        private FoodOrganizer                $organizer,
        private FoodRepositoryInterface      $repository,
        private FoodOrderRepositoryInterface $orderRepository,
    )
    {
    }

    public function __invoke(AddBurgerRequest $request): AddBurgerResponse
    {
        $order = $this->orderRepository->findById($request->orderId);
        Assert::notNull($order, 'Order not found.');
        $burger = $this->factory->build($request->orderId, $request->title);
        $burger = $this->organizer->make($burger);
        $this->repository->add($burger);

        return new AddBurgerResponse($burger->getId());
    }

}
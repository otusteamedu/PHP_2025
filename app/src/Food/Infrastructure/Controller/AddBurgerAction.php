<?php
declare(strict_types=1);


namespace App\Food\Infrastructure\Controller;

use App\Food\Application\UseCase\AddBurger\AddBurgerRequest;
use App\Food\Application\UseCase\AddBurger\AddBurgerUseCase;
use App\Shared\Infrastructure\Controller\BaseAction;
use App\Shared\Infrastructure\Http\Request;

class AddBurgerAction extends BaseAction
{
    public function __construct(private readonly AddBurgerUseCase $makeBurgerUseCase)
    {
    }

    public function __invoke(Request $request)
    {
        $title = $request->post('title');
        $orderId = $request->post('order_id');
        $ingredients = $request->post('ingredients');
        $command = new AddBurgerRequest($orderId, $title, ...$ingredients);
        $result = ($this->makeBurgerUseCase)($command);

        return $this->responseSuccess($result)->asJson();
    }

}
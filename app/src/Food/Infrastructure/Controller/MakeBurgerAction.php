<?php
declare(strict_types=1);


namespace App\Food\Infrastructure\Controller;

use App\Food\Application\UseCase\MakeBurger\MakeBurgerRequest;
use App\Food\Application\UseCase\MakeBurger\MakeBurgerUseCase;
use App\Shared\Infrastructure\Controller\BaseAction;
use App\Shared\Infrastructure\Http\Request;

class MakeBurgerAction extends BaseAction
{
    public function __construct(private readonly MakeBurgerUseCase $makeBurgerUseCase)
    {
    }

    public function __invoke(Request $request)
    {
        $title = $request->post('title');
        $ingredients = $request->post('ingredients');
        $command = new MakeBurgerRequest($title, ...$ingredients);
        $result = ($this->makeBurgerUseCase)($command);

        return $this->responseSuccess($result)->asJson();
    }

}
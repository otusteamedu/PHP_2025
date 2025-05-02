<?php
declare(strict_types=1);


namespace App\Food\Infrastructure\Controller;

use App\Food\Application\UseCase\MakeBurger\MakeBurgerRequest;
use App\Food\Application\UseCase\MakeBurger\MakeBurgerUseCase;
use App\Shared\Domain\Service\AssertService;
use App\Shared\Infrastructure\Controller\BaseAction;
use App\Shared\Infrastructure\Http\Request;

class MakeBurgerAction extends BaseAction
{
    private MakeBurgerUseCase $makeBurgerUseCase;

    public function __construct(MakeBurgerUseCase $makeBurgerUseCase)
    {
        $this->makeBurgerUseCase = $makeBurgerUseCase;
    }

    public function __invoke(Request $request)
    {
        $title = $request->post('title');
        AssertService::string($title, message: 'Title cannot be empty');
        $command = new MakeBurgerRequest($title);
        $result = ($this->makeBurgerUseCase)($command);

        return $this->responseSuccess($result)->asJson();
    }

}
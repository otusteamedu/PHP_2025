<?php

declare(strict_types=1);

namespace App\User\Infrastructure\Controller;

use App\Shared\Application\Command\CommandHandlerInterface;
use App\Shared\Domain\Service\AssertService;
use App\Shared\Infrastructure\Controller\BaseAction;
use App\Shared\Infrastructure\Http\Request;
use App\User\Application\UseCase\Command\AddUser\AddUserCommand;
use App\User\Application\UseCase\Command\AddUser\AddUserCommandHandler;

class AddUserAction extends BaseAction
{
    private CommandHandlerInterface $handler;

    public function __construct()
    {
        $this->handler = new AddUserCommandHandler();
    }

    public function __invoke(Request $request)
    {
        $email = $request->post('email');
        AssertService::string($email);
        $name = $request->post('name');
        AssertService::string($name);
        $command = new AddUserCommand($email, $name);
        $result = ($this->handler)($command);

        return $this->responseSuccess($result)->asJson();
    }
}

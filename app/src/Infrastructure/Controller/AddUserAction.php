<?php
declare(strict_types=1);


namespace App\Infrastructure\Controller;

use App\Application\Command\CommandHandlerInterface;
use App\Application\UseCase\Command\AddUser\AddUserCommand;
use App\Application\UseCase\Command\AddUser\AddUserCommandHandler;
use App\Domain\Service\AssertService;
use App\Infrastructure\Http\Request;

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
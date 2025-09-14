<?php

namespace App\UserInterface\Api\User;

use App\Application\DeleteUser\DeleteUserCommand;
use App\Application\DeleteUser\DeleteUserHandler;
use App\UserInterface\Api\User\Request\ActualizeUserRequest;
use App\UserInterface\Api\User\Request\DeleteUseRequest;
use App\Application\ActualizeUser\ActualizeUserQuery;
use App\Application\ActualizeUser\ActualizeUserHandler;
use App\Application\FindUser\FindUserHandler;
use App\Application\FindUser\FindUserQuery;

final readonly class UserController
{
    public function __construct(
        private ActualizeUserHandler $actualizeUserHandler,
        private DeleteUserHandler $deleteUserHandler,
        private FindUserHandler $findUserHandler,
    ){
    }

    public function actualize(ActualizeUserRequest $request): int
    {
        $query = new ActualizeUserQuery(
            name: $request->name,
            email: $request->email,
            password: $request->password,
            role: $request->role,
        );

        $output = $this->actualizeUserHandler->__invoke($query);

        return $output->id;
    }

    public function delete(DeleteUseRequest $request): void
    {
        $command = new DeleteUserCommand($request->id);

        $this->deleteUserHandler->__invoke($command);
    }

    public function find(int $id)
    {
        $query = new FindUserQuery($id);

        return $this->findUserHandler->__invoke($query);
    }
}

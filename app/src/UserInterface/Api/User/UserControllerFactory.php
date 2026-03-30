<?php

namespace App\UserInterface\Api\User;

use App\Application\ActualizeUser\ActualizeUserHandler;
use App\Application\DeleteUser\DeleteUserHandler;
use App\Application\FindUser\FindUserHandler;
use App\Connect\Connect;
use App\Infrastructure\Persistense\Mapping\UserMapper;
use App\Infrastructure\Persistense\Repository\UserRepository;

final class UserControllerFactory
{
    public static function create(): UserController
    {
        $pdo = new Connect()->connect();
        $mapper = new UserMapper($pdo, IdentityMapRegistry::user());
        $repository = new UserRepository($mapper);
        $actualizeUserHandler = new ActualizeUserHandler($repository);
        $deletedUserHandler = new DeleteUserHandler($repository);
        $findUserHandler = new FindUserHandler($repository);

        return new UserController($actualizeUserHandler, $deletedUserHandler, $findUserHandler);
    }
}
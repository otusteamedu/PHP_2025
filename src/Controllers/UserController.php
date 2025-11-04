<?php

namespace Blarkinov\PhpDbCourse\Controllers;

use Blarkinov\PhpDbCourse\Models\User\User;
use Blarkinov\PhpDbCourse\Models\User\UserMapper;
use Blarkinov\PhpDbCourse\Service\Database\Fabric;
use Blarkinov\PhpDbCourse\Service\UserDestroyValidator;
use Blarkinov\PhpDbCourse\Service\UserIndexValidator;
use Blarkinov\PhpDbCourse\Service\UserShowValidator;
use Blarkinov\PhpDbCourse\Service\UserStoreValidator;
use Blarkinov\PhpDbCourse\Service\UserUpdateValidator;

class UserController
{
    private UserMapper $mapper;

    public function __construct()
    {
        $this->mapper = new UserMapper((new Fabric)->create());
    }

    public function index(): array
    {
        (new UserIndexValidator)->validate();
        return $this->mapper->getAll((int)$_GET['offset'], (int)$_GET['limit']);
    }

    public function update(array $params = []): array
    {
        (new UserUpdateValidator)->validate($params['id']);
        return ['id' => $this->mapper->update($params['id'])];
    }
    public function show(array $params = []): ?User
    {
        (new UserShowValidator)->validate($params['id']);
        return $this->mapper->findByID($params['id']);
    }

    public function store(): array
    {

        (new UserStoreValidator)->validate();
        $id = $this->mapper->create();
        return ['id' => $id];
    }

    public function destroy(array $params = []): array
    {
        (new UserDestroyValidator)->validate($params['id']);
        return ['id' => $this->mapper->delete($params['id'])];
    }
}

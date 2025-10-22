<?php

namespace Blarkinov\PhpDbCourse\Controllers;

use Blarkinov\PhpDbCourse\Http\Response;
use Blarkinov\PhpDbCourse\Models\Event\WorkerEvent;
use Blarkinov\PhpDbCourse\Models\User\User;
use Blarkinov\PhpDbCourse\Models\User\UserMapper;
use Blarkinov\PhpDbCourse\Service\Validator;

class UserController
{
    private Response $response;
    private Validator $validator;
    private UserMapper $mapper;

    public function __construct()
    {
        $this->response = new Response();
        $this->validator = new Validator();
        $this->mapper = new UserMapper();
    }

    public function index(): array
    {
        return $this->mapper->getAll();
    }

    public function update(array $params = []): array
    {
        $this->validator->update($params['id']);
        return ['id'=>$this->mapper->update($params['id'])];
    }
    public function show(array $params = []): ?User
    {
        $this->validator->show($params['id']);
        return $this->mapper->findByID($params['id']);
    }

    public function store(): array
    {

        $this->validator->store();
        $id = $this->mapper->create();
        return ['id' => $id];
    }

    public function destroy(array $params = []): array
    {
        $this->validator->destroy($params['id']);
        return ['id'=>$this->mapper->delete($params['id'])];
    }
}

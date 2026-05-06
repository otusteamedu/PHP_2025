<?php

declare(strict_types=1);

namespace App\Controller\Http\Api\User;

use App\Core\Http\Response;
use App\Controller\Http\AbstractController;
use App\Domain\UserManagement\Entity\User;
use App\Domain\UserManagement\Model\CreateUserModel;
use App\Domain\UserManagement\Model\GetUsersModel;
use App\Domain\UserManagement\Model\UpdateUserEmailModel;
use App\Domain\UserManagement\UserService;
use Customer41\MultiException\MultiException;

class UserController extends AbstractController
{
    private readonly UserService $userService;

    public function __construct()
    {
        $this->userService = new UserService();
        parent::__construct();
    }

    public function createUser(): Response
    {
        try {
            $payload = $this->request->getPayload();
            $createUser = CreateUserDTO::fromArray($payload);
            $createdUser = $this->userService->createUser(
                new CreateUserModel(
                    firstName: $createUser->firstName,
                    lastName: $createUser->lastName,
                    email: $createUser->email,
                    birthDate: $createUser->birthDate,
                ),
            );
            $data['success'] = true;
            $data['user'] = $createdUser->toArray();
            $httpCode = 200;
        } catch (MultiException $validationErrors) {
            $data['success'] = false;
            $errors = [];
            foreach ($validationErrors as $error) {
                $errors[] = $error->getMessage();
            }
            $data['message'] = $validationErrors->getMessage();
            $data['details'] = $errors;
            $httpCode = $validationErrors->getCode();
        } catch (\Throwable $e) {
            $data['success'] = false;
            $data['message'] = $e->getMessage();
            $httpCode = $e->getCode();
        }

        return new Response(json_encode($data), $httpCode, ['Content-Type: application/json; charset=utf-8']);
    }

    public function getUser(): Response
    {
        try {
            $id = $this->request->getQueryParam('id');
            $id = is_numeric($id) ? (int) $id : throw new \Exception('Пользователь не найден.', 404);
            $user = $this->userService->findUser($id);
            $data['success'] = true;
            $data['user'] = $user->toArray();
            $httpCode = 200;
        } catch (\Throwable $e) {
            $data['success'] = false;
            $data['message'] = $e->getMessage();
            $httpCode = $e->getCode();
        }

        return new Response(json_encode($data), $httpCode, ['Content-Type: application/json; charset=utf-8']);
    }

    public function getUsers(): Response
    {
        try {
            $payload = $this->request->getPayload();
            $getUsers = GetUsersDTO::fromArray($payload);
            $users = $this->userService->findUsers(
                new GetUsersModel(
                    lastId: $getUsers->lastId,
                    limit: $getUsers->limit,
                ),
            );

            $data['success'] = true;
            $data['users'] = array_map(static fn(User $user) => $user->toArray(), $users->toArray());
            $data['pager'] = ['lastId' => $users->max('id'), 'totalItems' => $users->count()];
            $httpCode = 200;
        } catch (\Throwable $e) {
            $data['success'] = false;
            $data['message'] = $e->getMessage();
            $httpCode = $e->getCode();
        }

        return new Response(json_encode($data), $httpCode, ['Content-Type: application/json; charset=utf-8']);
    }

    public function updateUserEmail(): Response
    {
        try {
            $id = $this->request->getQueryParam('id');
            $id = is_numeric($id) ? (int) $id : throw new \Exception('Пользователь не найден.', 404);
            $newEmail = $this->request->getPayload()['newEmail']
                ?? throw new \Exception('Неверное тело запроса.', 400);
            $updatedUser = $this->userService->updateUserEmail(
                new UpdateUserEmailModel(userId: $id, newEmail: $newEmail)
            );
            $data['success'] = true;
            $data['user'] = $updatedUser->toArray();
            $httpCode = 200;
        } catch (\Throwable $e) {
            $data['success'] = false;
            $data['message'] = $e->getMessage();
            $httpCode = $e->getCode();
        }

        return new Response(json_encode($data), $httpCode, ['Content-Type: application/json; charset=utf-8']);
    }

    public function deleteUser(): Response
    {
        try {
            $id = $this->request->getQueryParam('id');
            $id = is_numeric($id) ? (int) $id : throw new \Exception('Пользователь не найден.', 404);
            $this->userService->deleteUser($id);
            $data['success'] = true;
            $data['message'] = 'Пользователь успешно удалён.';
            $httpCode = 200;
        } catch (\Throwable $e) {
            $data['success'] = false;
            $data['message'] = $e->getMessage();
            $httpCode = $e->getCode();
        }

        return new Response(json_encode($data), $httpCode, ['Content-Type: application/json; charset=utf-8']);
    }
}

<?php

declare(strict_types=1);

namespace App\Controller\Http\Api\UserManagement;

use App\Core\Http\Controller\Base\AbstractController;
use App\Core\Http\Message\Request;
use App\Core\Http\Message\Response;
use App\Domain\UserManagement\Entity\User;
use App\Domain\UserManagement\Model\CreateUserModel;
use App\Domain\UserManagement\Model\GetUsersModel;
use App\Domain\UserManagement\Model\UpdateUserEmailModel;
use App\Domain\UserManagement\UserService;

class UserManagementController extends AbstractController
{
    public function __construct(
        private readonly Request $request,
        private readonly UserService $userService,
    ) {
    }

    public function createUser(): Response
    {
        return $this->handleOperation(
            operation: function() {
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

                return $createdUser->toArray();
            },
        );
    }

    public function getUser(): Response
    {
        return $this->handleOperation(
            operation: function() {
                $id = $this->request->getQueryParam('id');
                $id = is_numeric($id) ? (int) $id : throw new \Exception('Пользователь не найден.', 404);
                $user = $this->userService->findUser($id);

                return $user->toArray();
            },
        );
    }

    public function getUsers(): Response
    {
        return $this->handleOperation(
            operation: function() {
                $payload = $this->request->getPayload();
                $getUsers = GetUsersDTO::fromArray($payload);
                $users = $this->userService->findUsers(
                    new GetUsersModel(
                        lastId: $getUsers->lastId,
                        limit: $getUsers->limit,
                    ),
                );
                $data['users'] = array_map(static fn(User $user) => $user->toArray(), $users->toArray());
                $data['pager'] = ['lastId' => $users->max('id'), 'totalItems' => $users->count()];

                return $data;
            },
        );
    }

    public function updateUserEmail(): Response
    {
        return $this->handleOperation(
            operation: function() {
                $id = $this->request->getQueryParam('id');
                $id = is_numeric($id) ? (int) $id : throw new \Exception('Пользователь не найден.', 404);
                $newEmail = $this->request->getPayload()['newEmail']
                    ?? throw new \Exception('Неверное тело запроса.', 400);
                $updatedUser = $this->userService->updateUserEmail(
                    new UpdateUserEmailModel(userId: $id, newEmail: $newEmail)
                );

                return $updatedUser->toArray();
            },
        );
    }

    public function deleteUser(): Response
    {
        return $this->handleOperation(
            operation: function() {
                $id = $this->request->getQueryParam('id');
                $id = is_numeric($id) ? (int) $id : throw new \Exception('Пользователь не найден.', 404);
                $this->userService->deleteUser($id);

                return ['message' => 'Пользователь успешно удалён.'];
            },
        );
    }
}

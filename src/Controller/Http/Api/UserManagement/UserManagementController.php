<?php

declare(strict_types=1);

namespace App\Controller\Http\Api\UserManagement;

use App\Core\Http\Controller\Base\AbstractController;
use App\Core\Http\Message\HttpStatus;
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

        return $this->json([
            'success' => true,
            'data' => ['user' => $createdUser->toArray()],
        ], HttpStatus::Created->value);
    }

    public function getUser(): Response
    {
        $id = $this->request->getIntQueryParam('id');
        $user = $this->userService->findUser($id);

        return $this->json([
            'success' => true,
            'data' => ['user' => $user->toArray()],
        ], HttpStatus::Ok->value);
    }

    public function getUsers(): Response
    {
        $payload = $this->request->getPayload();
        $getUsers = GetUsersDTO::fromArray($payload);
        $users = $this->userService->findUsers(
            new GetUsersModel(
                lastId: $getUsers->lastId,
                limit: $getUsers->limit,
            ),
        );

        return $this->json([
            'success' => true,
            'data' => [
                'users' => array_map(
                    static fn(User $user) => $user->toArray(),
                    $users->toArray(),
                ),
                'pager' => [
                    'lastId' => $users->maxId(),
                    'totalItems' => $users->count()
                ],
            ],
        ], HttpStatus::Ok->value);
    }

    public function updateUserEmail(): Response
    {
        $id = $this->request->getIntQueryParam('id');
        $payload = $this->request->getPayload();
        $updatedUser = $this->userService->updateUserEmail(
            new UpdateUserEmailModel(userId: $id, newEmail: $payload['newEmail'])
        );

        return $this->json([
            'success' => true,
            'data' => ['user' => $updatedUser->toArray()],
        ], HttpStatus::Ok->value);
    }

    public function deleteUser(): Response
    {
        $id = $this->request->getIntQueryParam('id');
        $this->userService->deleteUser($id);

        return $this->json([
            'success' => true,
            'data' => ['message' => 'Пользователь успешно удалён.'],
        ], HttpStatus::Ok->value);
    }
}

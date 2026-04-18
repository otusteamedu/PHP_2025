<?php

declare(strict_types=1);

namespace App\Presentation\Controller\User;

use App\Application\UseCase;
use App\Domain\Exception\UserAlreadyExistsException;
use App\Domain\Exception\UserNotFoundException;
use App\Presentation\Validation\UserValidator;
use App\Presentation\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Контроллер для управления пользователями.
 */
readonly class UserController
{
    /**
     * @param UserValidator $validator Валидатор данных пользователя.
     * @param UseCase\CreateUserUseCase $createUserUseCase Use case для создания пользователя.
     * @param UseCase\GetUserByIdUseCase $getUserByIdUseCase Use case для получения пользователя по ID.
     * @param UseCase\UpdateUserUseCase $updateUserUseCase Use case для обновления пользователя.
     * @param UseCase\DeleteUserUseCase $deleteUserUseCase Use case для удаления пользователя.
     */
    public function __construct(
        private UserValidator              $validator,
        private UseCase\CreateUserUseCase  $createUserUseCase,
        private UseCase\GetUserByIdUseCase $getUserByIdUseCase,
        private UseCase\UpdateUserUseCase  $updateUserUseCase,
        private UseCase\DeleteUserUseCase  $deleteUserUseCase
    ) {
    }

    /**
     * Создает нового пользователя.
     *
     * @param Request $request PSR-7 запрос.
     * @param Response $response PSR-7 ответ.
     * @return Response Ответ с результатом операции.
     * @throws \JsonException
     */
    public function createUser(Request $request, Response $response): Response
    {
        $data = (array)$request->getParsedBody();

        try {
            $this->validator->validate($data);
        } catch (ValidationException $exception) {
            $response->getBody()->write(json_encode(['errors' => $exception->getErrors()], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $user = $this->createUserUseCase->execute($data);
        } catch (UserAlreadyExistsException $exception) {
            $response->getBody()->write(json_encode(['error' => $exception->getMessage()], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(409);
        }

        $responseData = [
            'message' => 'User created successfully.',
            'user' => [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
            ]
        ];

        $response->getBody()->write(json_encode($responseData, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    /**
     * Получает информацию о пользователе по его ID.
     *
     * @param Request $request PSR-7 запрос.
     * @param Response $response PSR-7 ответ.
     * @param array $args Аргументы маршрута (содержит 'id').
     * @return Response Ответ с данными пользователя или ошибкой.
     * @throws \JsonException
     */
    public function getUser(Request $request, Response $response, array $args): Response
    {
        $id = (int)$args['id'];

        try {
            $user = $this->getUserByIdUseCase->execute($id);
        } catch (UserNotFoundException $exception) {
            $response->getBody()->write(json_encode(['error' => $exception->getMessage()], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $responseData = [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
            'telegram_id' => $user->getTelegramId(),
            'gender' => $user->getGender(),
            'weight' => $user->getWeight(),
            'height' => $user->getHeight(),
            'born' => $user->getBorn()->format('Y-m-d'),
        ];

        $response->getBody()->write(json_encode($responseData, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    /**
     * Обновляет данные существующего пользователя.
     *
     * @param Request $request PSR-7 запрос.
     * @param Response $response PSR-7 ответ.
     * @param array $args Аргументы маршрута (содержит 'id').
     * @return Response Ответ с результатом операции.
     * @throws \JsonException
     */
    public function updateUser(Request $request, Response $response, array $args): Response
    {
        $id = (int)$args['id'];
        $data = [];

        $contentType = $request->getHeaderLine('Content-Type');
        if (str_starts_with($contentType, 'application/x-www-form-urlencoded')) {
            parse_str($request->getBody()->getContents(), $data);
        }

        try {
            $this->validator->validateUpdate($data);
        } catch (ValidationException $exception) {
            $response->getBody()->write(json_encode(['errors' => $exception->getErrors()], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $user = $this->updateUserUseCase->execute($id, $data);
        } catch (UserNotFoundException $exception) {
            $response->getBody()->write(json_encode(['error' => $exception->getMessage()], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $responseData = [
            'message' => 'User updated successfully.',
            'user' => [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail(),
            ]
        ];

        $response->getBody()->write(json_encode($responseData, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    /**
     * Удаляет пользователя по его ID.
     *
     * @param Request $request PSR-7 запрос.
     * @param Response $response PSR-7 ответ.
     * @param array $args Аргументы маршрута (содержит 'id').
     * @return Response Ответ с результатом операции.
     * @throws \JsonException
     */
    public function deleteUser(Request $request, Response $response, array $args): Response
    {
        $id = (int)$args['id'];

        try {
            $this->deleteUserUseCase->execute($id);
        } catch (UserNotFoundException $exception) {
            $response->getBody()->write(json_encode(['error' => $exception->getMessage()], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }

        $response->getBody()->write(json_encode(['message' => 'User deleted successfully.'], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}

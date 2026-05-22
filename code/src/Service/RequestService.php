<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\RequestEntity;
use App\Enum\RequestStatus;
use App\Exception\RequestNotFoundException;
use App\Queue\QueuePublisherInterface;
use App\Repository\RequestRepository;
use InvalidArgumentException;
use Throwable;

/**
 * Сервис работы с запросами на обработку
 */
final class RequestService
{
    public function __construct(
        private readonly RequestRepository $repository,
        private readonly QueuePublisherInterface $publisher,
    ) {
    }

    /**
     * Создает запрос и отправляет его идентификатор в очередь
     *
     * @param array<string, mixed> $payload Данные HTTP-запроса
     *
     * @return RequestEntity
     */
    public function create(array $payload): RequestEntity
    {
        $title = $this->getTitle($payload);
        $description = $this->getDescription($payload);

        $request = $this->repository->create($title, $description);

        try {
            $this->publisher->publish(json_encode([
                'request_id' => $request->getId(),
            ], JSON_THROW_ON_ERROR));
        } catch (Throwable $exception) {
            $this->repository->updateStatus(
                $request->getId(),
                RequestStatus::Failed,
                $exception->getMessage(),
                true,
            );

            throw $exception;
        }

        return $request;
    }

    /**
     * Возвращает запрос по идентификатору
     *
     * @param int $requestId Идентификатор запроса
     *
     * @return RequestEntity
     */
    public function get(int $requestId): RequestEntity
    {
        if ($requestId < 1) {
            throw new InvalidArgumentException('Идентификатор запроса должен быть положительным числом');
        }

        $request = $this->repository->findById($requestId);

        if ($request === null) {
            throw new RequestNotFoundException('Запрос с request ID ' . $requestId . ' не найден');
        }

        return $request;
    }

    /**
     * Производит валидацию поля и возвращает проверенное название запроса
     *
     * @param array<string, mixed> $payload Данные HTTP-запроса
     *
     * @return string
     */
    private function getTitle(array $payload): string
    {
        if (!isset($payload['title']) || !is_string($payload['title'])) {
            throw new InvalidArgumentException('Поле title обязательно');
        }

        $title = trim($payload['title']);

        if ($title === '') {
            throw new InvalidArgumentException('Поле title не должно быть пустым');
        }

        if (mb_strlen($title) > 255) {
            throw new InvalidArgumentException('Поле title не должно содержать больше 255 символов');
        }

        return $title;
    }

    /**
     * Возвращает проверенное описание запроса
     *
     * @param array<string, mixed> $payload Данные HTTP-запроса
     *
     * @return string|null
     */
    private function getDescription(array $payload): ?string
    {
        if (!array_key_exists('description', $payload) || $payload['description'] === null) {
            return null;
        }

        if (!is_string($payload['description'])) {
            throw new InvalidArgumentException('Поле description должно быть строкой');
        }

        $description = trim($payload['description']);

        if (mb_strlen($description) > 1024) {
            throw new InvalidArgumentException('Поле description не должно содержать больше 1024 символов');
        }

        return $description ?: null;
    }
}

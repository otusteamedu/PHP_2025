<?php

declare(strict_types=1);

namespace App\Service;

use App\Enum\RequestStatus;
use App\Repository\RequestRepository;
use InvalidArgumentException;
use JsonException;
use RuntimeException;
use Throwable;

/**
 * Обработчик сообщений о запросах из очереди RabbitMQ
 */
final class RequestMessageHandler
{
    public function __construct(
        private readonly RequestRepository $repository,
    ) {
    }

    /**
     * Обрабатывает сообщение из очереди и обновляет статус запроса
     *
     * @param string $message JSON-сообщение из RabbitMQ
     *
     * @return void
     */
    public function handle(string $message): void
    {
        $requestId = $this->getRequestId($message);
        $request = $this->repository->findById($requestId);

        if ($request === null) {
            throw new RuntimeException('Запрос с ID ' . $requestId . ' не найден');
        }

        try {
            $this->repository->updateStatus($requestId, RequestStatus::Processing);

            // Имитация долгой обработки запроса, чтобы клиент мог увидеть промежуточный статус
            sleep(30);

            if ($requestId % 3 === 0) {
                // Имитация ошибки обработки каждой третьей заявки
                throw new RuntimeException('Имитация ошибки обработки заявки');
            }

            $this->repository->updateStatus($requestId, RequestStatus::Completed, null, true);
            echo 'Запрос #' . $requestId . ' обработан' . PHP_EOL;
        } catch (Throwable $exception) {
            $this->repository->updateStatus($requestId, RequestStatus::Failed, $exception->getMessage(), true);
            throw $exception;
        }
    }

    /**
     * Возвращает идентификатор запроса из сообщения очереди
     *
     * @param string $message JSON-сообщение из RabbitMQ
     *
     * @return int
     */
    private function getRequestId(string $message): int
    {
        try {
            $payload = json_decode($message, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new InvalidArgumentException('Некорректное сообщение из очереди');
        }

        if (!is_array($payload) || !isset($payload['request_id']) || !is_int($payload['request_id'])) {
            throw new InvalidArgumentException('В сообщении отсутствует корректный request_id');
        }

        return $payload['request_id'];
    }
}

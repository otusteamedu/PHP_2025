<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\RequestEntity;
use App\Enum\RequestStatus;
use PDO;
use RuntimeException;

/**
 * Репозиторий запросов на обработку
 */
final class RequestRepository
{
    public function __construct(
        private readonly PDO $connection,
    ) {
    }

    /**
     * Создает запрос на обработку в базе данных
     *
     * @param string $title Название запроса
     * @param ?string $description Описание запроса
     *
     * @return RequestEntity
     */
    public function create(string $title, ?string $description): RequestEntity
    {
        $statement = $this->connection->prepare(
            'INSERT INTO requests (title, description, status)
             VALUES (:title, :description, :status)
             RETURNING id, title, description, status, error_message, created_at, updated_at, processed_at'
        );

        $statement->execute([
            'title' => $title,
            'description' => $description,
            'status' => RequestStatus::Queued->value,
        ]);

        $row = $statement->fetch();

        if ($row === false) {
            throw new RuntimeException('Не удалось создать запрос на обработку');
        }

        return $this->createEntity($row);
    }

    /**
     * Ищет запрос на обработку по идентификатору
     *
     * @param int $id Идентификатор запроса
     *
     * @return ?RequestEntity
     */
    public function findById(int $id): ?RequestEntity
    {
        $statement = $this->connection->prepare(
            'SELECT id, title, description, status, error_message, created_at, updated_at, processed_at
             FROM requests
             WHERE id = :id'
        );

        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row !== false ? $this->createEntity($row) : null;
    }

    /**
     * Обновляет статус запроса на обработку
     *
     * @param int $id Идентификатор запроса
     * @param RequestStatus $status Новый статус
     * @param ?string $errorMessage Текст ошибки обработки
     * @param bool $markProcessed Нужно ли записать время завершения обработки
     *
     * @return void
     */
    public function updateStatus(
        int $id,
        RequestStatus $status,
        ?string $errorMessage = null,
        bool $markProcessed = false,
    ): void {
        $processedSql = $markProcessed ? ', processed_at = CURRENT_TIMESTAMP' : '';

        $statement = $this->connection->prepare(
            'UPDATE requests
             SET status = :status,
                 error_message = :error_message,
                 updated_at = CURRENT_TIMESTAMP' . $processedSql . '
             WHERE id = :id',
        );

        $statement->execute([
            'id' => $id,
            'status' => $status->value,
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Создает сущность запроса из строки таблицы
     *
     * @param array<string, mixed> $row Строка из таблицы requests
     *
     * @return RequestEntity
     */
    private function createEntity(array $row): RequestEntity
    {
        return new RequestEntity(
            (int) $row['id'],
            (string) $row['title'],
            $row['description'] === null ? null : (string) $row['description'],
            RequestStatus::from((string) $row['status']),
            $row['error_message'] === null ? null : (string) $row['error_message'],
            (string) $row['created_at'],
            (string) $row['updated_at'],
            $row['processed_at'] === null ? null : (string) $row['processed_at'],
        );
    }
}

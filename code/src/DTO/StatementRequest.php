<?php

declare(strict_types=1);

namespace App\DTO;

use JsonException;

/**
 * DTO с данными заявки на формирование банковской выписки.
 */
final class StatementRequest
{
    /**
     * @param string $requestId ID заявки.
     * @param string $accountNumber Номер счета.
     * @param string $dateFrom Дата начала периода.
     * @param string $dateTo Дата окончания периода.
     * @param string|null $email Email для отправки выписки.
     * @param string $createdAt Дата создания заявки.
     */
    public function __construct(
        public readonly string $requestId,
        public readonly string $accountNumber,
        public readonly string $dateFrom,
        public readonly string $dateTo,
        public readonly ?string $email,
        public readonly string $createdAt,
    ) {
    }

    /**
     * Проверяет, указан ли email для отправки выписки.
     *
     * @return bool
     */
    public function hasEmail(): bool
    {
        return $this->email !== null && $this->email !== '';
    }

    /**
     * @return array{
     *     request_id: string,
     *     account_number: string,
     *     date_from: string,
     *     date_to: string,
     *     email: string|null,
     *     created_at: string
     * }
     */
    public function toArray(): array
    {
        return [
            'request_id' => $this->requestId,
            'account_number' => $this->accountNumber,
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
            'email' => $this->email,
            'created_at' => $this->createdAt,
        ];
    }

    /**
     * @param array<string, mixed> $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['request_id'] ?? '',
            $data['account_number'] ?? '',
            $data['date_from'] ?? '',
            $data['date_to'] ?? '',
            isset($data['email']) && $data['email'] !== '' ? $data['email'] : null,
            $data['created_at'] ?? '',
        );
    }

    /**
     * @param string $json JSON-представление заявки.
     * @return self
     * @throws JsonException
     */
    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return self::fromArray(is_array($data) ? $data : []);
    }

    /**
     * @return string
     * @throws JsonException
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }
}

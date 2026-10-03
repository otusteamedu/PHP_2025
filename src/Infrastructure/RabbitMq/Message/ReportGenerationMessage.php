<?php

declare(strict_types=1);

namespace App\Infrastructure\RabbitMq\Message;

readonly class ReportGenerationMessage implements MessagePayloadInterface
{
    private const array FIELDS = ['reportId', 'clientName', 'dateFrom', 'dateTo', 'reportType', 'email'];

    public function __construct(
        public string $reportId,
        public string $clientName,
        public string $dateFrom,
        public string $dateTo,
        public string $reportType,
        public string $email,
    ) {
    }

    public static function fromArray(array $data): self
    {
        foreach (self::FIELDS as $field) {
            if (!array_key_exists($field, $data) || !is_string($data[$field])) {
                throw new \InvalidArgumentException(
                    sprintf('Missing or invalid field "%s" in message payload', $field),
                );
            }
        }

        return new self(
            ...array_intersect_key($data, array_flip(self::FIELDS)),
        );
    }

    public function toArray(): array
    {
        $result = [];

        foreach (self::FIELDS as $field) {
            $result[$field] = $this->$field;
        }

        return $result;
    }
}

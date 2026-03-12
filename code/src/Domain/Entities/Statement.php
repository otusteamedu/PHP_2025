<?php

declare(strict_types=1);

namespace Queues\Domain\Entities;

class Statement
{
    private function __construct(
        public readonly string $dateFrom,
        public readonly string $dateTo,
        public readonly string $email,
        public readonly string $id,
        public readonly string $requestDt,
        public readonly string $content = '',
    ) {
    }

    public static function create(string $dateFrom, string $dateTo, string $email): self {
        return self::fromData($dateFrom, $dateTo, $email);
    }

    public static function withContent(Statement $original, string $content): self
    {
        return new self(
            $original->dateFrom,
            $original->dateTo,
            $original->email,
            $original->id,
            $original->requestDt,
            $content
        );
    }

    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return self::fromData(
            $data['dateFrom'],
            $data['dateTo'],
            $data['email'],
            $data['id'] ?? null,
            $data['requestDt'] ?? null,
            $data['content'] ?? ''
        );
    }

    public function toJson(): string
    {
        return json_encode([
            'id' => $this->id,
            'requestDt' => $this->requestDt,
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'email' => $this->email,
            'content' => $this->content,
        ], JSON_THROW_ON_ERROR);
    }
    private static function fromData(
        string $dateFrom,
        string $dateTo,
        string $email,
        ?string $id = null,
        ?string $requestDt = null,
        string $content = ''
    ): self {
        return new self(
            $dateFrom,
            $dateTo,
            $email,
            $id ?? uniqid('stmt_', true),
            $requestDt ?? date('Y-m-d H:i:s'),
            $content
        );
    }
}

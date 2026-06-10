<?php

declare(strict_types=1);

namespace MkdBot\Domain\ValueObject;

readonly class RagSearchResult
{
    /**
     * @param RagSearchSource[] $sources Источники, использованные для ответа
     */
    public function __construct(
        private string $answer = '',
        private array $sources = [],
        private bool $success = true,
        private int $errorCode = 0,
        private string $errorMessage = '',
    ) {
    }

    public function getAnswer(): string
    {
        return $this->answer;
    }

    /**
     * @return RagSearchSource[]
     */
    public function getSources(): array
    {
        return $this->sources;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @param RagSearchSource[] $sources
     */
    public static function success(string $answer, array $sources = []): self
    {
        return new self(
            answer: $answer,
            sources: $sources,
            success: true,
            errorCode: 0,
            errorMessage: '',
        );
    }

    public static function error(int $code, string $message): self
    {
        return new self(
            answer: '',
            sources: [],
            success: false,
            errorCode: $code,
            errorMessage: $message,
        );
    }
}

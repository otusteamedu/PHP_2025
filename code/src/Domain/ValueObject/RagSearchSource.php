<?php

declare(strict_types=1);

namespace MkdBot\Domain\ValueObject;

readonly class RagSearchSource
{
    public function __construct(
        private string $filename,
        private ?string $fileId = null,
        private ?float $score = null,
        private string $text = '',
    ) {
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * Идентификатор файла в векторном хранилище
     */
    public function getFileId(): ?string
    {
        return $this->fileId;
    }

    /**
     * Релевантность источника
     */
    public function getScore(): ?float
    {
        return $this->score;
    }

    /**
     * Текстовый фрагмент из источника
     */
    public function getText(): string
    {
        return $this->text;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            filename: $data['filename'] ?? 'Неизвестный файл',
            fileId: $data['file_id'] ?? null,
            score: isset($data['score']) ? (float)$data['score'] : null,
            text: $data['text'] ?? '',
        );
    }
}

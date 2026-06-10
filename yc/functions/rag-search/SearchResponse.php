<?php

declare(strict_types=1);

class SearchSource
{
    public function __construct(
        public readonly string $filename,
        public readonly ?string $fileId = null,
        public readonly ?float $score = null,
        public readonly string $text = ''
    ) {}

    /** Из результата file_search_call: { file_id, filename, score, text } */
    public static function fromFileSearchResult(array $result): self
    {
        return new self(
            filename: $result['filename'] ?? 'Неизвестный файл',
            fileId: $result['file_id'] ?? null,
            score: isset($result['score']) ? (float)$result['score'] : null,
            text: $result['text'] ?? ''
        );
    }

    /** Из аннотации file_citation: { type, file_id, filename, index } */
    public static function fromFileCitation(array $annotation): self
    {
        return new self(
            filename: $annotation['filename'] ?? 'Неизвестный файл',
            fileId: $annotation['file_id'] ?? null,
            score: null,
            text: ''
        );
    }

    public function toArray(): array
    {
        $result = [
            'filename' => $this->filename,
        ];
        if ($this->fileId !== null) {
            $result['file_id'] = $this->fileId;
        }
        if ($this->score !== null) {
            $result['score'] = $this->score;
        }
        if ($this->text !== '') {
            $result['text'] = $this->text;
        }
        return $result;
    }
}

class SearchResponse
{
    /**
     * @param SearchSource[] $sources
     */
    private function __construct(
        public readonly bool $success,
        public readonly string $answer,
        public readonly array $sources,
        public readonly int $errorCode,
        public readonly string $errorMessage
    ) {}

    /** @param SearchSource[] $sources */
    public static function success(string $answer, array $sources = []): self
    {
        return new self(true, $answer, $sources, 0, '');
    }

    public static function error(int $code, string $message): self
    {
        return new self(false, '', [], $code, $message);
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function toArray(): array
    {
        if ($this->success) {
            return [
                'success' => true,
                'answer' => $this->answer,
                'sources' => array_map(fn(SearchSource $s) => $s->toArray(), $this->sources),
            ];
        }

        return [
            'success' => false,
            'error' => [
                'code' => $this->errorCode,
                'message' => $this->errorMessage,
            ],
        ];
    }
}

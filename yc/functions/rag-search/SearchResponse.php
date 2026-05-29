<?php

declare(strict_types=1);

class SearchResponse
{
    /**
     * @param bool $success Успешность поиска
     * @param string $answer Текст ответа (пустой при ошибке)
     * @param array $sources Список источников (пустой при ошибке)
     * @param int $errorCode Код HTTP-ошибки (0 при успехе)
     * @param string $errorMessage Описание ошибки (пустое при успехе)
     */
    private function __construct(
        public readonly bool $success,
        public readonly string $answer,
        public readonly array $sources,
        public readonly int $errorCode,
        public readonly string $errorMessage
    ) {}

    /**
     * Создание успешного ответа поиска
     *
     * @param string $answer Текст ответа
     * @param array[] $sources Список источников
     */
    public static function success(string $answer, array $sources = []): self
    {
        return new self(true, $answer, $sources, 0, '');
    }

    // Создание ответа с ошибкой поиска
    public static function error(int $code, string $message): self
    {
        return new self(false, '', [], $code, $message);
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    // Преобразование в ассоциативный массив для JSON-сериализации
    public function toArray(): array
    {
        if ($this->success) {
            return [
                'success' => true,
                'answer' => $this->answer,
                'sources' => $this->sources,
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

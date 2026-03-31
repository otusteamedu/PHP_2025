<?php

declare(strict_types=1);

namespace App\Dto;

use JsonException;

class ResponseDto
{
    public function __construct(
        public string $message,
        public array $data = [],
        public int $code = 200,
    ) {
    }

    /**
     * @throws JsonException
     */
    public function getResponseMessage(): string
    {
        return json_encode([
            'message' => $this->message,
            'data' => $this->data,
        ], JSON_THROW_ON_ERROR);
    }
}

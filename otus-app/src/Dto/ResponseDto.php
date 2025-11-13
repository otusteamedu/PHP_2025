<?php

declare(strict_types=1);

namespace App\Dto;

use JsonException;

class ResponseDto
{
    public function __construct(
        public string $message = 'Success',
        public int $code = 200,
        public array $data = [],
    ){
    }

    /**
     * @throws JsonException
     */
    public function getResponseJson(): string
    {
        $resultData = [
            'message' => $this->message,
            'data' => $this->data,
        ];

        return json_encode($resultData, JSON_THROW_ON_ERROR);
    }
}

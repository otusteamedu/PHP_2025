<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Response;

use JsonException;

final readonly class JsonResponse
{
    public function __construct(
        private array $data,
        private int $statusCode = 200,
    ) {
    }

    /**
     * @throws JsonException
     */
    public function send(): void
    {
        http_response_code($this->statusCode);

        header('Content-Type: application/json');

        echo json_encode(
            $this->data,
            JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT
        );
    }
}

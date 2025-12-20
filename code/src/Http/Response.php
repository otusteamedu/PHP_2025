<?php

declare(strict_types=1);

namespace src\Http;

readonly class Response
{
    public function __construct(
        public mixed $data,
        public int $statusCode = 200
    ) {}
}

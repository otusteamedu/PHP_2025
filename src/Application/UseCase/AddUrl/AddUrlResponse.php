<?php

namespace App\Application\UseCase\AddUrl;

class AddUrlResponse
{
    public function __construct(
        public readonly int $id,
    )
    {
    }
}
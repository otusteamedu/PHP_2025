<?php

namespace App\Application\UseCase\AddUrl;

class AddUrlRequest
{
    public function __construct(
        public readonly string $url
    )
    {
    }

}
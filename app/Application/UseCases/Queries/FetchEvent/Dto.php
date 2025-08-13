<?php

namespace App\Application\UseCases\Queries\FetchEvent;

class Dto
{
    public function __construct(
        public readonly array $params
    ) {}
}
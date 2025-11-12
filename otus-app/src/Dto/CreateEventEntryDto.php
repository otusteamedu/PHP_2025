<?php

namespace App\Dto;

class CreateEventEntryDto
{
    public function __construct(
        public string $message,
    ){
    }
}

<?php

namespace Blarkinov\Hw1500\Application\UseCase\Response;

class BadRequest
{
    public function send(): array
    {
        return [
            'httpcode' => 400,
            'message' => 'Bad Request',
        ];
    }
}

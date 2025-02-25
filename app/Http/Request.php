<?php

namespace App\Http;

class Request
{
    /**
     * @return array
     */
    public function getPost(): array
    {
        return $_POST;
    }
}
<?php

namespace App\Interface;

interface ResponseHandlerInterface {
    public function sendResponse(int $statusCode, array $data): void;
}
<?php

declare(strict_types=1);

namespace AppV2;

use JsonException;

class RequestBodyService
{
    /**
     * @throws JsonException
     */
    public function getDecodedJsonBody(): array
    {
        $requestBody = file_get_contents('php://input');

        return json_decode($requestBody, true, 512, JSON_THROW_ON_ERROR);
    } //нужно вынести в отдельную директорию
}

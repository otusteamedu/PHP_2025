<?php

namespace App\Service;

use JsonException;

class RequestDataService
{
    /**
     * @throws JsonException
     */
    public function getJsonDecodedRequestData(): array
    {
        $rawData = file_get_contents('php://input');

        return json_decode($rawData, true, 512, JSON_THROW_ON_ERROR);
    }
}

<?php declare(strict_types=1);

namespace App\Http;

/**
 * Class Response
 * @package App\Http
 */
class Response
{
    /**
     * @param int $statusCode
     * @param mixed $data
     * @return string
     */
    public function send(int $statusCode, mixed $data): string
    {
        header("Content-Type: application/json");
        http_response_code($statusCode);

        return json_encode($data);
    }
}

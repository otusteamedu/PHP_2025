<?php

declare(strict_types=1);

namespace App\Http;

/**
 * Class Response
 * @package App\Http
 */
class Response
{
    const string FORMAT_RAW = 'raw';
    const string FORMAT_JSON = 'json';
    /**
     * @var string
     */
    private string $format = self::FORMAT_RAW;
    /**
     * @var mixed
     */
    private mixed $data;
    /**
     * @var int
     */
    private int $statusCode = 200;

    /**
     * @param int $statusCode
     * @param mixed $data
     * @param string $format
     * @return Response
     */
    public static function create(int $statusCode, mixed $data, string $format = Response::FORMAT_JSON): Response
    {
        $response = new Response();
        $response->setStatusCode($statusCode);
        $response->setData($data);
        $response->setFormat($format);

        return $response;
    }

    /**
     * @return string
     */
    public function send(): string
    {
        $data = $this->getData();

        if ($this->getFormat() === self::FORMAT_JSON) {
            header("Content-Type: application/json");
            $data = json_encode($data);
        }

        http_response_code($this->getStatusCode());

        return $data;
    }

    /**
     * @return string
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @param string $format
     * @return void
     */
    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    /**
     * @return mixed
     */
    public function getData(): mixed
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     * @return void
     */
    public function setData(mixed $data): void
    {
        $this->data = $data;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     * @return void
     */
    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }
}

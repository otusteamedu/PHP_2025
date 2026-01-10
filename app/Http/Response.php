<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Http;

use \JsonException;

class Response
{

    private array $arData;

    public function setHeader(string $header): Response
    {
        header($header);
        return $this;
    }

    /**
     * @throws JsonException
     */
    public function json(array $arData): string
    {
        $this->setHeader('Content-Type: application/json');
        return json_encode($arData, JSON_THROW_ON_ERROR);
    }

    public function send(int $statusCode, array $arData): Response
    {
        http_response_code($statusCode);
        $this->arData = $arData;
        return $this;
    }

    /**
     * @throws JsonException
     */
    public function __toString(): string
    {
        return $this->json($this->arData);
    }
}
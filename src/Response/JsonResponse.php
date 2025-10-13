<?php

declare(strict_types=1);

namespace Dinargab\Homework11\Response;

class JsonResponse
{
    private $data;
    private $statusCode;
    private $headers;
    private $encodingOptions;

    public function __construct(
        $data,
        int $statusCode = 200,
        array $headers = [],
        int $encodingOptions = JSON_UNESCAPED_UNICODE
    ) {
        $this->data = $data;
        $this->statusCode = $statusCode;
        $this->headers = array_merge(['Content-Type' => 'application/json; charset=utf-8'], $headers);
        $this->encodingOptions = $encodingOptions;
    }

    public function send()
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        echo json_encode($this->data, $this->encodingOptions);
    }
}

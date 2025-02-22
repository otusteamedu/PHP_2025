<?php declare(strict_types=1);

namespace App\Http;

class Response
{
    public function setHeader($header): Response
    {
        header($header);
        return $this;
    }

    public function json($data): string
    {
        $this->setHeader('Content-Type: application/json');
        return json_encode($data);
    }

    public function send($statusCode, $data): string
    {
        return $this->json($data);
    }
}

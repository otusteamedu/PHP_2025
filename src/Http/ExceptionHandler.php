<?php

namespace App\Http;

use Throwable;

class ExceptionHandler
{
    private Response $response;
    private int $statusCode;
    private array $errors;

    public function __construct()
    {
        $this->response = new Response();
        $this->statusCode = 500;
        $this->errors = [];
    }

    /**
     * @param Throwable $exception
     * @return Response
     */
    public function render(Throwable $exception): Response
    {
        if (method_exists($exception, 'getStatusCode')) {
            $this->statusCode = $exception->getStatusCode();
        }

        if (method_exists($exception, 'getErrors')) {
            $this->errors = $exception->getErrors();
            $message = $this->errorsToJSONString();
        } else {
            $message = $exception->getMessage();
        }

        $this->response->setMessage($message);

        $this->response->setStatusCode($this->statusCode);

        return $this->response;
    }

    /**
     * @return string
     */
    private function errorsToJSONString(): string
    {
        return json_encode($this->errors, JSON_UNESCAPED_UNICODE);
    }
}
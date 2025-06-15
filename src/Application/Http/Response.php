<?php

namespace App\Application\Http;

class Response
{
    protected array $data;
    protected int $code;
    protected ?string $message;

    public function __construct(array $data = [], int $code = 200, string $message = null) {
        $this->message = $message;
        $this->code = $code;
        $this->data = $data;
    }

    public function init(): void {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($this->code);

        $success = true;

        if ($this->code >= 400) {
            $success = false;
        }

        $responseData = [
            'success' => $success
        ];

        if (empty($this->message) === false) {
            $responseData['message'] = $this->message;
        }

        if (empty($this->data) === false) {
            $responseData['data'] = $this->data;
        }

        echo json_encode($responseData);
    }
}
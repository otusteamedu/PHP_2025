<?php

namespace Producer\Application\Http;

class Response
{
    /** @var array */
    protected array $data;

    /** @var int */
    protected int $code;

    /** @var string|null */
    protected ?string $message;

    /**
     * @param array $data
     * @param int $code
     * @param string|null $message
     */
    public function __construct(array $data = [], int $code = 200, string $message = null)
    {
        $this->message = $message;
        $this->code = $code;
        $this->data = $data;
    }

    /**
     * @return void
     */
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
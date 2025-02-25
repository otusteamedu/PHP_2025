<?php

namespace App\Http;

class Response
{
    private string $message;

    public function __construct()
    {
        $this->message = '';
    }

    /**
     * @param string $message
     * @return void
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getMessage();
    }

}
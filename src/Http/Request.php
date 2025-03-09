<?php

namespace App\Http;

class Request
{
    /**
     * @return bool
     */
    public function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * @return array
     */
    public function getPost(): array
    {
        return $_POST;
    }

    /**
     * @return string
     */
    public function getRawBody(): string
    {
        return file_get_contents('php://input');
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key): mixed
    {
        return $this->getPost()[$key]
            ?? json_decode($this->getRawBody(), true)[$key]
            ?? null;
    }
}

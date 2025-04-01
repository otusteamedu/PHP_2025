<?php

declare(strict_types=1);

namespace App\Http;

use RuntimeException;

/**
 * Class Request
 * @package App\Http
 */
class Request
{
    /**
     * @return string
     */
    public function getMethod(): string
    {
        if (isset($_SERVER['REQUEST_METHOD'])) {
            return strtoupper($_SERVER['REQUEST_METHOD']);
        }

        return 'GET';
    }

    /**
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->getMethod() === 'GET';
    }

    /**
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->getMethod() === 'POST';
    }

    /**
     * @return bool
     */
    public function isDelete(): bool
    {
        return $this->getMethod() === 'DELETE';
    }

    /**
     * @param string $name
     * @param mixed $defaultValue
     * @return mixed
     */
    public function post(string $name, mixed $defaultValue = null): mixed
    {
        return $_POST[$name] ?? $defaultValue;
    }

    /**
     * @return string the request body
     */
    public function getRawBody(): string
    {
        return file_get_contents('php://input');
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            $requestUri = $_SERVER['REQUEST_URI'];
            if ($requestUri !== '' && $requestUri[0] !== '/') {
                $requestUri = preg_replace('/^(http|https):\/\/[^\/]+/i', '', $requestUri);
            }
        } else {
            throw new RuntimeException('Unable to determine the request URI.');
        }

        return rtrim($requestUri, '/');
    }
}

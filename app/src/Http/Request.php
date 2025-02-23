<?php declare(strict_types=1);

namespace App\Http;

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
    public function isPost(): bool
    {
        return $this->getMethod() === 'POST';
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
}

<?php
declare(strict_types=1);


namespace App\Infrastructure\Http;

class Request
{
    private const string METHOD_POST = 'POST';
    private array $post = [];
    private array $get = [];

    public function __construct()
    {
        $this->buildPost();
        $this->buildGet();
    }

    public function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === static::METHOD_POST;
    }

    public function post(string $key): ?string
    {
        return $this->post[$key] ?? null;
    }

    public function postAll(): array
    {
        return $this->post;
    }

    public function get(string $key): ?string
    {
        return $this->get[$key] ?? null;
    }

    public function getAll(): array
    {
        return $this->get;
    }

    private function buildPost(): void
    {
        $requestBody = file_get_contents('php://input');
        if ($postBody = json_decode($requestBody, true)) {
            $this->post = $postBody;
            return;
        }
        if (is_string($requestBody) and strlen($requestBody) > 0) {
            foreach (explode(',', $requestBody) as $postParam) {
                preg_match("/[\w\d]*=.*/m", $postParam, $matches);
                $param = explode('=', $matches[0], 2);
                $this->post[$param[0]] = $param[1];
            };
            return;
        }
    }

    private function buildGet(): void
    {
        $this->get = $_GET;
    }

    public function getUrl(): string
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }


}
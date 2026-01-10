<?php
declare(strict_types=1);


namespace App\Http;

class Request
{
    private const string METHOD_POST = 'POST';
    private array $post = [];
    private array $get = [];

    public function __construct()
    {
        $this->buildPost();
    }

    public function isPost(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === static::METHOD_POST;
    }

    public function post(string $key): mixed
    {
        return $this->post[$key] ?? null;
    }

    public function postAll(): array
    {
        return $this->post;
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

}
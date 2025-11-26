<?php
declare(strict_types=1);

namespace App\Http;

class Request
{
    private array $server;
    private array $post;

    /**
     * @param array $server
     * @param array $post
     */
    public function __construct(array $server, array $post) {
        $this->server = $server;
        $this->post = $post;
    }

    public static function fromGlobals(): self
    {
        return new self($_SERVER, $_POST);
    }

    /**
     * @return string
     */
    public function getServerRequestMethod(): string
    {
        return $this->server['REQUEST_METHOD'];
    }

    /**
     * @param string $key
     * @return string
     */
    public function getPostInputByKey(string $key): string
    {
        return $this->post[$key] ?? '';
    }
}

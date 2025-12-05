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

    /**
     * @return self
     */
    public static function fromGlobals(): self
    {
        return new self($_SERVER, $_POST);
    }

    /**
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->server['REQUEST_METHOD'] === 'POST';
    }

    /**
     * @return string
     */
    public function getEmailsInput(): string
    {
        return trim($this->post['emails'] ?? '');
    }
}

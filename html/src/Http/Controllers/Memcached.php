<?php

declare(strict_types=1);

namespace Otus\Http\Controllers;

use Laminas\Diactoros\Response;
use Otus\Connections\Memcached as Connection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class Memcached
{
    public function __construct(
        protected Connection $connection,
    ) {
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $key = 'key';
        $value = 'value';

        $response = new Response();
        $response
            ->getBody()
            ->write(json_encode([
                'eq' => ($this->connection->set($key, $value) && $this->connection->get($key) === $value),
            ]));

        return $response;
    }
}

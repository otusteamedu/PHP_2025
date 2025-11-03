<?php

declare(strict_types=1);

namespace Otus\Http\Controllers;

use Laminas\Diactoros\Response;
use Otus\Connections\Postgres as Connection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

readonly class Postgres
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
        $response = new Response();
        $response
            ->getBody()
            ->write(json_encode([
                'eq' => (($stmt = $this->connection->query('SELECT 1')) && $stmt->fetchColumn() === 1),
            ]));

        return $response;
    }
}

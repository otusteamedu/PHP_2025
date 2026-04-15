<?php

declare(strict_types=1);

namespace Otus\Queue\Presentation\Http;

use Otus\Queue\Infrastructure\Http\Request;
use Otus\Queue\Infrastructure\Http\Response\ResponseInterface;
use Otus\Queue\Infrastructure\Http\ResponseFactory;

final class FallbackController
{
    /**
     * @param Request $request
     *
     * @return ResponseInterface
     */
    public function __invoke(Request $request): ResponseInterface
    {
        return match ($this->getAccept($request)) {
            'application/json' => $this->toJson(),

            default => $this->toHtml(),
        };
    }

    /**
     * @return ResponseInterface
     */
    public function toJson(): ResponseInterface
    {
        return ResponseFactory::toJson(
            body: [
                'content' => 'Not Found.',
            ],
            statusCode: 404,
        );
    }

    /**
     * @return ResponseInterface
     */
    public function toHtml(): ResponseInterface
    {
        return ResponseFactory::toHtml(
            body: '<h1>404</h1><p>Page not found.</p>',
            statusCode: 404,
        );
    }

    /**
     * @param Request $request
     *
     * @return string|null
     */
    private function getAccept(Request $request): ?string
    {
        return $request->headers->get('Accept');
    }
}

<?php

declare(strict_types=1);

namespace Otus\Queue\Infrastructure\Http;

use Closure;
use Otus\Queue\Infrastructure\Http\Exception\AbstractHttpException;
use Otus\Queue\Infrastructure\Http\Response\Html;
use Otus\Queue\Infrastructure\Http\Response\Json;
use Otus\Queue\Infrastructure\Http\Response\ResponseInterface;
use Otus\Queue\Infrastructure\Http\Response\Stream;

final class ResponseFactory
{
    /**
     * @param Request $request
     * @param AbstractHttpException $exception
     *
     * @return ResponseInterface
     */
    public static function exception(Request $request, AbstractHttpException $exception): ResponseInterface
    {
        return match (self::getAccept($request)) {
            'application/json' => self::toJson([
                'content' => $exception->getMessage(),
            ], $exception->getStatusCode()),

            default => self::toHtml($exception->getMessage(), $exception->getStatusCode()),
        };
    }

    /**
     * @param Closure $callback
     * @param int $statusCode
     * @param array $headers
     *
     * @return ResponseInterface
     */
    public static function toStream(Closure $callback, int $statusCode = 200, array $headers = []): ResponseInterface
    {
        return Stream::create(
            callback: $callback,
            statusCode: $statusCode,
            headers: $headers,
        );
    }

    /**
     * @param array $body
     * @param int $statusCode
     *
     * @return ResponseInterface
     */
    public static function toJson(array $body, int $statusCode = 200): ResponseInterface
    {
        return Json::create(
            body: $body,
            statusCode: $statusCode,
        );
    }

    /**
     * @param string $body
     * @param int $statusCode
     *
     * @return ResponseInterface
     */
    public static function toHtml(string $body, int $statusCode = 200): ResponseInterface
    {
        return Html::create(
            body: $body,
            statusCode: $statusCode,
        );
    }

    /**
     * @param Request $request
     *
     * @return string|null
     */
    private static function getAccept(Request $request): ?string
    {
        return $request->headers['Accept'] ?? null;
    }
}

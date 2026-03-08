<?php

declare(strict_types=1);

namespace Otus\Queue\Tests\Infrastructure\Http;

use Otus\Queue\Infrastructure\Component\Collection;
use Otus\Queue\Infrastructure\Http\Exception\NotFoundHttpException;
use Otus\Queue\Infrastructure\Http\Method;
use Otus\Queue\Infrastructure\Http\Request;
use Otus\Queue\Infrastructure\Http\Response\Html;
use Otus\Queue\Infrastructure\Http\Response\Json;
use Otus\Queue\Infrastructure\Http\ResponseFactory;
use PHPUnit\Framework\TestCase;

final class ResponseFactoryTest extends TestCase
{
    public function testExceptionReturnsJsonResponseWhenAcceptIsJson(): void
    {
        $request = new Request(
            method: Method::GET,
            uri: '/',
            headers: Collection::make(['Accept' => 'application/json']),
            queryParams: Collection::make(),
            body: Collection::make(),
        );

        $response = ResponseFactory::exception($request, new NotFoundHttpException());

        self::assertInstanceOf(Json::class, $response);
        self::assertSame(404, $response->getStatusCode());
    }

    public function testExceptionReturnsHtmlResponseByDefault(): void
    {
        $request = new Request(
            method: Method::GET,
            uri: '/',
            headers: Collection::make(),
            queryParams: Collection::make(),
            body: Collection::make(),
        );

        $response = ResponseFactory::exception($request, new NotFoundHttpException());

        self::assertInstanceOf(Html::class, $response);
        self::assertSame(404, $response->getStatusCode());
    }
}

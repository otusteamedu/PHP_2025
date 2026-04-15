<?php

declare(strict_types=1);

namespace Otus\Queue\Infrastructure\Http\Response {
    function http_response_code(int $code): int
    {
        $GLOBALS['otus_response_code'] = $code;

        return $code;
    }

    function header(string $header): void
    {
        $GLOBALS['otus_headers'][] = $header;
    }

    function ob_implicit_flush(?bool $enable = null): void
    {
        $GLOBALS['otus_implicit_flush_called'] = true;
    }

    function ob_get_level(): int
    {
        return $GLOBALS['otus_ob_level'] ?? 0;
    }

    function ob_end_flush(): bool
    {
        if (($GLOBALS['otus_ob_level'] ?? 0) > 0) {
            $GLOBALS['otus_ob_level']--;
            $GLOBALS['otus_ob_end_flush_calls']++;

            return true;
        }

        return false;
    }

    function flush(): void
    {
        $GLOBALS['otus_flush_called'] = true;
    }
}

namespace Otus\Queue\Tests\Infrastructure\Http\Response {
    use Otus\Queue\Infrastructure\Http\Response\Html;
    use Otus\Queue\Infrastructure\Http\Response\Json;
    use Otus\Queue\Infrastructure\Http\Response\Raw;
    use Otus\Queue\Infrastructure\Http\Response\Stream;
    use PHPUnit\Framework\TestCase;

    final class ResponseSendTest extends TestCase
    {
        protected function setUp(): void
        {
            $GLOBALS['otus_response_code'] = null;
            $GLOBALS['otus_headers'] = [];
            $GLOBALS['otus_flush_called'] = false;
            $GLOBALS['otus_implicit_flush_called'] = false;
            $GLOBALS['otus_ob_level'] = 0;
            $GLOBALS['otus_ob_end_flush_calls'] = 0;
        }

        public function testHtmlSendOutputsBodyAndStatusCode(): void
        {
            $response = Html::create('<p>ok</p>', 201);

            ob_start();
            $response->send();
            $out = ob_get_clean();

            self::assertSame('<p>ok</p>', $out);
            self::assertSame(201, $response->getStatusCode());
            self::assertSame(201, $GLOBALS['otus_response_code']);
            self::assertContains('Content-Type: text/html; charset=utf-8', $GLOBALS['otus_headers']);
        }

        public function testJsonSendOutputsEncodedBodyAndStatusCode(): void
        {
            $response = Json::create(['ok' => true], 202);

            ob_start();
            $response->send();
            $out = ob_get_clean();

            self::assertStringContainsString('"ok": true', $out);
            self::assertSame(202, $response->getStatusCode());
            self::assertSame(202, $GLOBALS['otus_response_code']);
            self::assertContains('Content-Type: application/json', $GLOBALS['otus_headers']);
        }

        public function testRawSendOutputsBodyStatusAndCustomHeader(): void
        {
            $response = Raw::create('raw-data', 206, ['X-Raw' => '1']);

            ob_start();
            $response->send();
            $out = ob_get_clean();

            self::assertSame('raw-data', $out);
            self::assertSame(206, $response->getStatusCode());
            self::assertSame(206, $GLOBALS['otus_response_code']);
            self::assertContains('X-Raw: 1', $GLOBALS['otus_headers']);
        }

        public function testStreamSendFlushesBuffersAndExecutesCallback(): void
        {
            $called = false;

            $response = Stream::create(
                static function () use (&$called): void {
                    $called = true;
                    echo 'stream-data';
                },
                200,
                ['X-Test' => '1']
            );

            $GLOBALS['otus_ob_level'] = 2;

            ob_start();
            $response->send();
            $out = ob_get_clean();

            self::assertTrue($called);
            self::assertSame('stream-data', $out);
            self::assertSame(200, $response->getStatusCode());
            self::assertSame(200, $GLOBALS['otus_response_code']);
            self::assertTrue($GLOBALS['otus_implicit_flush_called']);
            self::assertTrue($GLOBALS['otus_flush_called']);
            self::assertSame(2, $GLOBALS['otus_ob_end_flush_calls']);
            self::assertContains('X-Test: 1', $GLOBALS['otus_headers']);
        }
    }
}

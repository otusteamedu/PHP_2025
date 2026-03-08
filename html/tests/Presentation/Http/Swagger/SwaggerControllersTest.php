<?php

declare(strict_types=1);

namespace Otus\Queue\Tests\Presentation\Http\Swagger;

use Otus\Queue\Infrastructure\Http\Response\Html;
use Otus\Queue\Infrastructure\Http\Response\Raw;
use Otus\Queue\Infrastructure\Template\TemplateInterface;
use Otus\Queue\Presentation\Http\Swagger\ApiController;
use Otus\Queue\Presentation\Http\Swagger\UiController;
use PHPUnit\Framework\TestCase;

final class SwaggerControllersTest extends TestCase
{
    public function testApiControllerReturnsRawJsonResponse(): void
    {
        $controller = new ApiController();

        $response = $controller();

        self::assertInstanceOf(Raw::class, $response);
        self::assertSame(200, $response->getStatusCode());

        $reflection = new \ReflectionClass($response);
        $body = $reflection->getProperty('body');

        self::assertStringContainsString('"openapi"', (string) $body->getValue($response));
    }

    public function testUiControllerRendersSwaggerTemplateAndReturnsHtmlResponse(): void
    {
        $template = new class () implements TemplateInterface {
            public string $template = '';

            public array $data = [];

            public function render(string $template, array $data = []): string
            {
                $this->template = $template;
                $this->data = $data;

                return '<h1>swagger</h1>';
            }
        };

        $controller = new UiController($template);

        $response = $controller();

        self::assertInstanceOf(Html::class, $response);
        self::assertSame(200, $response->getStatusCode());
        self::assertSame('swagger/index', $template->template);
        self::assertSame('Swagger', $template->data['urls'][0]['name']);
        self::assertSame('/swagger/api', $template->data['urls'][0]['url']);
    }
}

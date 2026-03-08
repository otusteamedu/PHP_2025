<?php

declare(strict_types=1);

namespace Otus\Queue\Tests\Presentation\Http;

use Otus\Queue\Application\Interface\ChatRepositoryInterface;
use Otus\Queue\Application\UseCase\Chat\History;
use Otus\Queue\Application\UseCase\Chat\Store;
use Otus\Queue\Domain\Entity\Message;
use Otus\Queue\Infrastructure\Component\Collection;
use Otus\Queue\Infrastructure\Http\Method;
use Otus\Queue\Infrastructure\Http\Request;
use Otus\Queue\Infrastructure\Http\Response\Html;
use Otus\Queue\Infrastructure\Http\Response\Json;
use Otus\Queue\Infrastructure\Http\Response\Stream;
use Otus\Queue\Infrastructure\Queue\Payload;
use Otus\Queue\Infrastructure\Queue\QueueInterface;
use Otus\Queue\Infrastructure\Template\TemplateInterface;
use Otus\Queue\Presentation\Http\Chat\HistoryController;
use Otus\Queue\Presentation\Http\Chat\IndexController;
use Otus\Queue\Presentation\Http\Chat\SseController;
use Otus\Queue\Presentation\Http\Chat\StoreController;
use Otus\Queue\Presentation\Http\FallbackController;
use PHPUnit\Framework\TestCase;

final class ControllersTest extends TestCase
{
    public function testHistoryControllerReturnsJsonWithHistory(): void
    {
        $repo = new class () implements ChatRepositoryInterface {
            public function store(Message $message): bool
            {
                return true;
            }

            public function history(int $limit = 20): array
            {
                return [new Message('a', 'b', 1)];
            }
        };

        $controller = new HistoryController(new History($repo));

        $response = $controller();

        self::assertInstanceOf(Json::class, $response);
        self::assertSame(200, $response->getStatusCode());
        ob_start();
        $response->send();
        $output = (string) ob_get_clean();

        self::assertSame([['author' => 'a', 'text' => 'b', 'createdAt' => 1]], json_decode($output, true));
    }

    public function testIndexControllerRendersChatTemplateAndReturnsHtml(): void
    {
        $template = new class () implements TemplateInterface {
            public string $template = '';

            public function render(string $template, array $data = []): string
            {
                $this->template = $template;

                return '<h1>chat</h1>';
            }
        };

        $controller = new IndexController($template);

        $response = $controller();

        self::assertInstanceOf(Html::class, $response);
        self::assertSame(200, $response->getStatusCode());
        self::assertSame('chat/index', $template->template);
    }

    public function testStoreControllerReturns201ForValidMessage(): void
    {
        $repo = new class () implements ChatRepositoryInterface {
            public function store(Message $message): bool
            {
                return true;
            }

            public function history(int $limit = 20): array
            {
                return [];
            }
        };

        $queue = new class () implements QueueInterface {
            public function push(string $connection, Payload $payload): void
            {
            }

            public function pull(string $connection, Payload $payload): void
            {
            }
        };

        $controller = new StoreController(new Store($repo, $queue));

        $response = $controller($this->request(Method::POST, '/chat/store', body: ['author' => 'alex', 'text' => 'hello']));

        self::assertInstanceOf(Json::class, $response);
        self::assertSame(201, $response->getStatusCode());
    }

    public function testStoreControllerReturns422ForValidationError(): void
    {
        $repo = new class () implements ChatRepositoryInterface {
            public function store(Message $message): bool
            {
                return true;
            }

            public function history(int $limit = 20): array
            {
                return [];
            }
        };

        $queue = new class () implements QueueInterface {
            public function push(string $connection, Payload $payload): void
            {
            }

            public function pull(string $connection, Payload $payload): void
            {
            }
        };

        $controller = new StoreController(new Store($repo, $queue));

        $response = $controller($this->request(Method::POST, '/chat/store', body: ['author' => ' ', 'text' => '']));

        self::assertInstanceOf(Json::class, $response);
        self::assertSame(422, $response->getStatusCode());
    }

    public function testSseControllerReturnsStreamAndInvokesQueuePullOnSend(): void
    {
        $queue = new class () implements QueueInterface {
            public int $pullCalls = 0;

            public function push(string $connection, Payload $payload): void
            {
            }

            public function pull(string $connection, Payload $payload): void
            {
                if ($connection === 'realtime' && $payload->get('exchange') === 'chat') {
                    $this->pullCalls++;
                }
            }
        };

        $controller = new SseController($queue);

        $response = $controller();

        self::assertInstanceOf(Stream::class, $response);

        $reflection = new \ReflectionClass($response);
        $callback = $reflection->getProperty('callback');
        $callback->setAccessible(true);
        $closure = $callback->getValue($response);
        $closure();

        self::assertSame(1, $queue->pullCalls);
    }

    public function testFallbackControllerSelectsResponseByAcceptHeader(): void
    {
        $controller = new FallbackController();

        $jsonResponse = $controller($this->request(Method::GET, '/404', headers: ['Accept' => 'application/json']));
        $htmlResponse = $controller($this->request(Method::GET, '/404'));

        self::assertInstanceOf(Json::class, $jsonResponse);
        self::assertInstanceOf(Html::class, $htmlResponse);
        self::assertSame(404, $jsonResponse->getStatusCode());
        self::assertSame(404, $htmlResponse->getStatusCode());
    }

    private function request(Method $method, string $uri, array $headers = [], array $body = []): Request
    {
        return new Request(
            method: $method,
            uri: $uri,
            headers: Collection::make($headers),
            queryParams: Collection::make(),
            body: Collection::make($body),
        );
    }
}

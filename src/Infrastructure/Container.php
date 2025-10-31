<?php

declare(strict_types=1);

namespace App\Infrastructure;

use App\Application\Port\BookSearchRepositoryInterface;
use App\Application\UseCase\SearchBooksHandler;
use App\Infrastructure\Elasticsearch\BookSearchRepository;
use App\Infrastructure\Elasticsearch\ClientFactory;
use Elastic\Elasticsearch\Client;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class Container implements ContainerInterface
{
    /** @var array<string, callable(self): mixed> */
    private array $definitions = [];

    /** @var array<string, mixed> */
    private array $instances = [];

    public function __construct()
    {
        $this->definitions = [
            Config::class => function (): Config {
                $envPath = __DIR__ . '/../../.env';
                // Adjust path if running from bin
                if (!is_file($envPath)) {
                    $envPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '.env';
                }
                return new Config($envPath);
            },
            ClientFactory::class => function (self $c): ClientFactory {
                return new ClientFactory($c->get(Config::class));
            },
            Client::class => function (self $c): Client {
                return $c->get(ClientFactory::class)->create();
            },
            BookSearchRepositoryInterface::class => function (self $c): BookSearchRepositoryInterface {
                return new BookSearchRepository($c->get(Client::class), $c->get(Config::class));
            },
            SearchBooksHandler::class => function (self $c): SearchBooksHandler {
                return new SearchBooksHandler($c->get(BookSearchRepositoryInterface::class));
            },
        ];
    }

    public function get(string $id)
    {
        if (array_key_exists($id, $this->instances)) {
            return $this->instances[$id];
        }
        if (!array_key_exists($id, $this->definitions)) {
            throw new class("No entry or class found for {$id}") extends \RuntimeException implements NotFoundExceptionInterface {};
        }
        $factory = $this->definitions[$id];
        $instance = $factory($this);
        $this->instances[$id] = $instance;
        return $instance;
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->definitions);
    }
}

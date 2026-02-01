<?php

declare(strict_types=1);

namespace App;

use App\Controllers\indexController;
use App\Controllers\loginController;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Service\Auth\AuthServiceInterface;
use App\Infrastructure\Service\Auth\AuthService;
use App\Domain\Service\Session\SessionInterface;
use App\Domain\Service\Storage\StorageInterface;
use App\Domain\Service\Text\TextProcessingServiceInterface;
use App\Domain\Service\View\ViewInterface;
use App\Infrastructure\Repository\PostgresUserRepository;
use App\Infrastructure\Service\Session\PhpSession;
use App\Infrastructure\Service\Storage\LocalFileSystemStorage;
use App\Infrastructure\Service\Text\SimpleTextProcessingService;
use App\Infrastructure\Service\View\PhpTemplateView;
use PDO;
use Psr\Container\ContainerInterface;

/**
 * Простой DI-контейнер для управления зависимостями.
 */
class Container implements ContainerInterface
{
    private array $services = [];
    private array $postgreParams = [];
    public function __construct()
    {
        $this->services = $this->registerServices();
        $this->postgreParams = [
            'database' => getenv('POSTGRES_DB'),
            'host' => getenv('POSTGRES_HOST'),
            'port' => getenv('POSTGRES_PORT'),
            'user' => getenv('POSTGRES_USER'),
            'password' => getenv('POSTGRES_PASSWORD'),
        ];
    }

    public function get(string $id)
    {
        if (!$this->has($id)) {
            throw new \Exception("Service not found: {$id}");
        }

        $service = $this->services[$id];
        
        if ($service instanceof \Closure) {
            return $service($this);
        }

        return $service;
    }

    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }

    private function registerServices(): array
    {
        // Определяем базовый путь для удобства
        $basePath = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__);

        return [
            PDO::class => function () {
                $dsn = "pgsql:host={$this->postgreParams['host']};port={$this->postgreParams['port']};dbname={$this->postgreParams['database']};";
                $user = $this->postgreParams['user'];
                $password = $this->postgreParams['password'];
                return new PDO($dsn, $user, $password);
            },

            // --- Репозитории (Интерфейс => Реализация) ---
            UserRepositoryInterface::class => function (ContainerInterface $c) {
                return new PostgresUserRepository($c->get(PDO::class));
            },

            // --- Сервисы (Интерфейс => Реализация) ---
            SessionInterface::class => fn() => new PhpSession(),
            StorageInterface::class => fn() => new LocalFileSystemStorage(),
            TextProcessingServiceInterface::class => fn() => new SimpleTextProcessingService(),
            ViewInterface::class => function () use ($basePath) {
                return new PhpTemplateView(
                    $basePath . '/src/resource/view',
                    $basePath . '/src/resource/view/header.php',
                    $basePath . '/src/resource/view/footer.html'
                );
            },
            
            indexController::class => function (ContainerInterface $c) {
                return new indexController(
                    $c->get(TextProcessingServiceInterface::class),
                    $c->get(ViewInterface::class),
                );
            },
        ];
    }
}

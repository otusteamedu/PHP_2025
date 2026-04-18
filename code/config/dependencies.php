<?php

declare(strict_types=1);

use App\Application\UseCase\CreateUserUseCase;
use App\Infrastructure\Repository\PostgresUserRepository;
use App\Presentation\Validation\UserValidator;
use App\Repository\UserRepositoryInterface;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

return function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        // 1. Настройка PDO
        PDO::class => function (ContainerInterface $c) {
            $host = getenv('POSTGRES_HOST');
            $port = getenv('POSTGRES_PORT');
            $db = getenv('POSTGRES_DB');
            $user = getenv('POSTGRES_USER');
            $pass = getenv('POSTGRES_PASSWORD');

            $dsn = "pgsql:host={$host};port={$port};dbname={$db}";

            try {
                return new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                // В реальном приложении здесь нужно логировать ошибку
                throw new \RuntimeException("Database connection failed: " . $e->getMessage(), (int)$e->getCode(), $e);
            }
        },

        // 2. Связывание интерфейса репозитория с его реализацией
        UserRepositoryInterface::class => \DI\autowire(PostgresUserRepository::class),

        // 3. Явное определение зависимостей для UserController
        // Хотя autowire должен справляться, явное определение может решить проблему
        UserValidator::class => \DI\autowire(),
        CreateUserUseCase::class => \DI\autowire(),
    ]);
};

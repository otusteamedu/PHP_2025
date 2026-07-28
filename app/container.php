<?php

declare(strict_types=1);

use App\Domain\Repository\TaskRepositoryInterface;
use App\Infrastructure\Database\Config\DatabaseConfigLoader;
use App\Infrastructure\Database\Connection\ConnectionFactory;
use App\Infrastructure\Database\Repository\TaskRepository;
use App\Infrastructure\Database\TaskDataMapper;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

require_once __DIR__ . '/../vendor/autoload.php';

$builder = new ContainerBuilder();

$builder->addDefinitions([

    PDO::class => static function (): PDO {
        $config = DatabaseConfigLoader::load();

        return ConnectionFactory::create($config);
    },

    TaskDataMapper::class => static function (ContainerInterface $container): TaskDataMapper {
        return new TaskDataMapper(
            $container->get(PDO::class),
        );
    },

    TaskRepositoryInterface::class => static function (ContainerInterface $container): TaskRepositoryInterface {
        return new TaskRepository(
            $container->get(TaskDataMapper::class),
        );
    },

]);

return $builder->build();

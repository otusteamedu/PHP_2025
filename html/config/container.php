<?php

declare(strict_types=1);

use Otus\DataMapper\Application\UseCase\Product\CreateProductUseCase;
use Otus\DataMapper\Application\UseCase\Product\DeleteProductUseCase;
use Otus\DataMapper\Application\UseCase\Product\GetProductsUseCase;
use Otus\DataMapper\Application\UseCase\Product\UpdateProductUseCase;
use Otus\DataMapper\Domain\Repository\ProductRepositoryInterface;
use Otus\DataMapper\Infrastructure\Bus\Bus;
use Otus\DataMapper\Infrastructure\Config\ArrayReader;
use Otus\DataMapper\Infrastructure\Config\Config;
use Otus\DataMapper\Infrastructure\Config\ConfigInterface;
use Otus\DataMapper\Infrastructure\Config\ReaderInterface;
use Otus\DataMapper\Infrastructure\Database\Connection;
use Otus\DataMapper\Infrastructure\Dic\Container;
use Otus\DataMapper\Infrastructure\Persistence\Mapper\ProductMapper;
use Otus\DataMapper\Infrastructure\Persistence\Repository\ProductRepository;
use Otus\DataMapper\Presentation\Console\DeleteProductCommand;
use Otus\DataMapper\Presentation\Console\InsertProductCommand;
use Otus\DataMapper\Presentation\Console\ListProductsCommand;
use Otus\DataMapper\Presentation\Console\UpdateProductCommand;

return [
    'singletons' => [
        // Domain
        ProductRepositoryInterface::class => static function (Container $container): ProductRepositoryInterface {
            return new ProductRepository(
                $container->get(ProductMapper::class)
            );
        },
        // Application
        CreateProductUseCase::class => static function (Container $container): CreateProductUseCase {
            return new CreateProductUseCase(
                $container->get(ProductRepositoryInterface::class)
            );
        },
        GetProductsUseCase::class => static function (Container $container): GetProductsUseCase {
            return new GetProductsUseCase(
                $container->get(ProductRepositoryInterface::class)
            );
        },
        UpdateProductUseCase::class => static function (Container $container): UpdateProductUseCase {
            return new UpdateProductUseCase(
                $container->get(ProductRepositoryInterface::class)
            );
        },
        DeleteProductUseCase::class => static function (Container $container): DeleteProductUseCase {
            return new DeleteProductUseCase(
                $container->get(ProductRepositoryInterface::class)
            );
        },
        // Infrastructure
        ReaderInterface::class => static function (): ReaderInterface {
            return new ArrayReader(dirname(__DIR__) . '/.env.php');
        },
        ConfigInterface::class => static function (Container $container): ConfigInterface {
            $list = $container->get(ReaderInterface::class)->get();

            return new Config($list);
        },
        Bus::class => static function (): Bus {
            return new Bus([
                'insert' => InsertProductCommand::class,
                'list' => ListProductsCommand::class,
                'update' => UpdateProductCommand::class,
                'delete' => DeleteProductCommand::class,
            ]);
        },
        Connection::class => static function (Container $container): Connection {
            /** @var ConfigInterface $config */
            $config = $container->get(ConfigInterface::class);

            return new Connection(
                $config->get('DATABASE_DSN'),
                $config->get('DATABASE_USERNAME'),
                $config->get('DATABASE_PASSWORD')
            );
        },
        PDO::class => static function (Container $container): PDO {
            return $container->get(Connection::class)->getPdo();
        },
        ProductMapper::class => static function (Container $container): ProductMapper {
            return new ProductMapper(
                $container->get(PDO::class),
                'products'
            );
        },
        // Presentation
        InsertProductCommand::class => static function (Container $container): InsertProductCommand {
            return new InsertProductCommand(
                $container->get(CreateProductUseCase::class)
            );
        },
        ListProductsCommand::class => static function (Container $container): ListProductsCommand {
            return new ListProductsCommand(
                $container->get(GetProductsUseCase::class)
            );
        },
        UpdateProductCommand::class => static function (Container $container): UpdateProductCommand {
            return new UpdateProductCommand(
                $container->get(UpdateProductUseCase::class)
            );
        },
        DeleteProductCommand::class => static function (Container $container): DeleteProductCommand {
            return new DeleteProductCommand(
                $container->get(DeleteProductUseCase::class)
            );
        },
    ],
    'definitions' => [],
];

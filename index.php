<?php

use App\Core\DatabaseConnection;
use App\Core\DIContainer;
use App\Core\Kernel;
use App\Core\Router;
use App\Repositories\DirectoryRepository;
use App\Repositories\FileRepository;
use App\Repositories\Interfaces\DirectoryRepositoryInterface;
use App\Repositories\Interfaces\FileRepositoryInterface;
use App\Service\FileService;
use App\Service\Interfaces\FileServiceInterface;
use Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv::createArrayBacked(__DIR__ . '/');
$dotenv->load();

$container = new DIContainer();

$container->singleton(PDO::class, fn() => DatabaseConnection::getConnection());

$container->bind(FileRepositoryInterface::class, fn() => new FileRepository());
$container->bind(DirectoryRepositoryInterface::class, fn() => new DirectoryRepository());

$container->bind(FileServiceInterface::class, function(DIContainer $container) {
    return new FileService(
        $container->make(FileRepositoryInterface::class),
        $container->make(DirectoryRepositoryInterface::class)
    );
});

$container->bind(Router::class, function(DIContainer $container) {
    return new Router($container);
});

$kernel = new Kernel($container);
print_r($kernel->handle());
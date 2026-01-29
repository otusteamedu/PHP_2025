#!/usr/bin/env php
<?php

declare(strict_types=1);

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

require_once __DIR__ . '/vendor/autoload.php';

$container = new ContainerBuilder();

$loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/config'));
$loader->load('services.yaml');

$container->setParameter('project_dir', __DIR__);

$container->compile(true);

try {
    $application = $container->get(Application::class);

    $application->run();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

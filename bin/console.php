#!/usr/bin/env php
<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Restaurant\DI\Container;
use Restaurant\Command\CreateBurgerCommand;
use Restaurant\Command\CreateCustomBurgerCommand;
use Restaurant\Command\CreatePizzaCommand;
use Restaurant\Command\ProcessOrderCommand;
use Restaurant\Command\QualityCheckCommand;

$container = new Container();
$container->register();

$command = $argv[1] ?? 'help';

try {
    switch ($command) {
        case 'create-burger':
            $cmd = $container->get(CreateBurgerCommand::class);
            $cmd->execute();
            break;

        case 'create-custom-burger':
            $cmd = $container->get(CreateCustomBurgerCommand::class);
            $cmd->execute();
            break;

        case 'create-pizza':
            $cmd = $container->get(CreatePizzaCommand::class);
            $cmd->execute();
            break;

        case 'process-order':
            $cmd = $container->get(ProcessOrderCommand::class);
            $cmd->execute();
            break;

        case 'quality-check':
            $cmd = $container->get(QualityCheckCommand::class);
            $cmd->execute();
            break;

        case 'help':
        default:
            echo "Доступные команды:\n";
            echo "  create-burger          - Создать простой бургер\n";
            echo "  create-custom-burger   - Создать кастомный бургер\n";
            echo "  create-pizza           - Создать пиццу\n";
            echo "  process-order          - Обработать заказ\n";
            echo "  quality-check          - Проверка качества\n";
            break;
    }
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage() . "\n";
    exit(1);
}

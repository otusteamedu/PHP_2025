<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use User\Php2025\Command\Command;

$options = getopt('', ['action:', 'index', 'title::', 'price::']);

$command = new Command();

$action = $options['action'];
$title = $options['title'] ?? null;
$price = $options['price'] ?? null;

$command->run($action, $title, $price);

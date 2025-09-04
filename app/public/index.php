<?php

declare(strict_types=1);

use App\Command\Command;

require_once __DIR__ . '/../../vendor/autoload.php';

$options = getopt('', ['event', 'action:', 'priority::', 'param1::', 'param2::']);

$action = $options['action'];
$priority = $options['priority'] ?? null;
$param1 = $options['param1'] ?? null;
$param2 = $options['param2'] ?? null;

$command = new Command()->run($action, $priority, $param1, $param2);

echo $command;

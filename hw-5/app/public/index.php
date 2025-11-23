<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

$definitions = require dirname(__DIR__) . '/config/services.php';
$container = new App\Infrastructure\DI\Container($definitions);

$verificationEmailCommand = $container->get('verificationEmailCommand');
$verificationEmailCommand->handle();

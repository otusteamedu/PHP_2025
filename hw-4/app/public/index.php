<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

$bracketValidator = new App\Domain\BracketValidator();
$bracketService   = new App\Application\BracketService($bracketValidator);
$httpController   = new App\UserInterface\HttpController($bracketService);

$httpController->handle();

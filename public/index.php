<?php

declare(strict_types=1);

use Kkonshin\Php2025ComposerPackage\Api;

require __DIR__ . '/../vendor/autoload.php';

$response = (new Api)->json();

print_r($response);
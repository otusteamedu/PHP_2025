<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use MarinaLubnik\PackageUppercase\StringToUppercase;

$processor = new StringToUppercase();
echo $processor->getStringToUpper('string');
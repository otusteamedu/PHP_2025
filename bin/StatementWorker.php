#!/usr/bin/env php
<?php
declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

use App\Config\AppConfig;
use App\Config\AppFactory;

$consumer = (new AppFactory(AppConfig::fromEnvironment()))->createStatementConsumer();
$consumer->execute();

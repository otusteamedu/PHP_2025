#!/usr/bin/php
<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Queues\Config\ContainerConfig;
use Queues\Presentation\Console\Worker;

ContainerConfig::getContainer()
    ->get(Worker::class)
    ->run();

#!/usr/bin/php
<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Api\Config\ContainerConfig;
use Api\Presentation\Console\Worker;

ContainerConfig::getContainer()
    ->get(Worker::class)
    ->run();

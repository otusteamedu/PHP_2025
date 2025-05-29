<?php

require_once __DIR__ . '/vendor/autoload.php';

$consumer = new \App\Queue\Consumer();
$consumer->listen();

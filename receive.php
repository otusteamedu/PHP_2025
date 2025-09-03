<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Infrastructure\Controllers\ReceiverController;

$controller = new ReceiverController();
$controller->runReceiver();
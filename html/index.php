<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Aovchinnikova\Hw15\Controller\EmailController;

$controller = new EmailController();
$controller->handleValidationRequest();
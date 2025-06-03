<?php

declare(strict_types=1);

use App\Controller\EmailController;
use App\Controller\IndexController;
use App\Controller\InfrastructureController;
use App\Controller\ParenthesisStringController;
use App\Controller\SessionController;

require_once __DIR__ . '/../vendor/autoload.php';

switch ($_SERVER['REQUEST_URI']) {
    case '/infrastructure-health-check':
        new InfrastructureController()->checkServiceHealth();
        break;
    case '/parenthesis-verifier':
        new ParenthesisStringController()->verifyParenthesisString();
        break;
    case '/session-storage-checker':
        new SessionController()->checkSessionStorage();
        break;
    default:
        new IndexController()->displayMainPage();
}

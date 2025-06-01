<?php

declare(strict_types=1);

use App\InfrastructureHealthCheck;
use App\ParenthesisStringVerifier;
use App\SessionStorageChecker;

require_once __DIR__ . '/../vendor/autoload.php';

switch ($_SERVER['REQUEST_URI']) {
    case '/infrastructure-health-check':
        $infrastructureHealthCheck = new InfrastructureHealthCheck()->run();
        echo '<h1>Infrastructure health check</h1>';
        foreach ($infrastructureHealthCheck->getServices() as $serviceName => $serviceMessage) {
            echo "<p>$serviceName: $serviceMessage</p>";
        }
        break;
    case '/parenthesis-verifier':
        $parenthesisStringVerifier = new ParenthesisStringVerifier()->run();
        header('Content-Type: application/json; charset=utf-8', response_code: $parenthesisStringVerifier->getHttpCode());
        echo json_encode(['response' => $parenthesisStringVerifier->getMessage()]);
        break;
    case '/session-storage-checker':
        ini_set('session.serialize_handler', 'php_serialize');
        session_start();
        $_SESSION['test_session_key_1'] = 'test_session_value_1';
        $_SESSION['test_session_key_2'] = 'test_session_value_2';
        $sessionStorageChecker = new SessionStorageChecker()->run();
        echo '<h1>Session storage checker</h1>';
        echo "<p>Session vars from Redis:</p>";
        foreach ($sessionStorageChecker->getSessionVars() as $key => $val) {
            echo "<p>$key => $val</p>";
        }
        break;
    default:
        header('Cache-Control: no-cache');
        echo '<h1>Welcome to main page</h1>';
        echo '<h2>Nginx balancer health check</h2>';
        echo "<p>Hostname (php container): {$_SERVER['HOSTNAME']}</p>";
        echo "<p>Server_addr (nginx node ip): {$_SERVER['SERVER_ADDR']}</p>";
}

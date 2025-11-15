<?php
require __DIR__ . "/vendor/autoload.php";

use App\Controller\EmailVerifierController;
use App\Response\ResponseJsonHandler;
use App\Session\RedisSessionManager;
use App\Validation\EmailValidator;
use App\Validation\PostRequestValidator;

ini_set('session.save_handler', 'redis');
ini_set('session.save_path', 'tcp://redis:6379');

session_start();

// Application entry point
try {
    $sessionManager = new RedisSessionManager();
    $controller = new EmailVerifierController(
        new PostRequestValidator($sessionManager),
        new EmailValidator(),
        new ResponseJsonHandler()
    );
    $controller->handleRequest();
} catch (Exception $e) {
    $responseHandler = new ResponseJsonHandler();
    $responseHandler->sendResponse(
        500,
        ['error' => 'Server error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')]
    );
}
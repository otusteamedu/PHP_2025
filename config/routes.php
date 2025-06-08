<?php

return [
    ['GET', '/', App\Controller\IndexController::class, 'displayMainPage'],
    ['GET', '/infrastructure-health-check', App\Controller\InfrastructureController::class, 'checkServiceHealth'],
    ['POST', '/parenthesis-verifier', App\Controller\ParenthesisStringController::class, 'verifyParenthesisString'],
    ['GET', '/session-storage-checker', App\Controller\SessionController::class, 'checkSessionStorage'],
    ['POST', '/emails-verifier', App\Controller\EmailController::class, 'verifyEmails'],
];

<?php

return [
    ['GET', '/', \App\Controller\Web\IndexController::class, 'displayMainPage'],
    ['GET', '/infrastructure-health-check', \App\Controller\Web\InfrastructureController::class, 'checkServiceHealth'],
    ['POST', '/parenthesis-verifier', \App\Controller\Web\ParenthesisStringController::class, 'verifyParenthesisString'],
    ['GET', '/session-storage-checker', \App\Controller\Web\SessionController::class, 'checkSessionStorage'],
    ['POST', '/emails-verifier', \App\Controller\Web\EmailController::class, 'verifyEmails'],
    ['POST', '/add-event', \App\Controller\Web\EventController::class, 'addEvent'],
    ['GET', '/get-event', \App\Controller\Web\EventController::class, 'getEvent'],
    ['DELETE', '/delete-events', \App\Controller\Web\EventController::class, 'deleteEvents'],
];

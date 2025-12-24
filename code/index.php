<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\App;

$emailsToCheck = [
    "test@example.com",
    "invalid-email",
    "user@unrealdomain.xyz"
];

$app = new App();
echo $app->run($emailsToCheck);
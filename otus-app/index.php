<?php

require __DIR__ . './../vendor/autoload.php';

use App\App;

$app = new App();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_SERVER['REQUEST_URI'] === '/clear') {
        $app->handleDeleteAllEvents();

        return;
    }

    $app->handleAddEvent();

    return;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $app->handleGetEvent();

    return;
}

http_response_code(400);
echo 'Invalid request method';

return;

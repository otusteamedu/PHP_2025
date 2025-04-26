<?php

declare(strict_types=1);

use App\Controllers\FileController;

return [
    '/files/list' => ['GET', [FileController::class, 'listFiles']],
    '/files/get/{id}' => ['GET', [FileController::class, 'fileInfo']],
    '/files/add' => ['POST', [FileController::class, 'addFile']],
    '/directories/add' => ['POST', [FileController::class, 'addDirectory']],
    '/directories/get/{id}' => ['GET', [FileController::class, 'directoryInfo']],
];
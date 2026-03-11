<?php
declare(strict_types=1);

namespace App\Http\OpenApi;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    description: 'REST API for queuing tasks and checking processing',
    title: 'Task Processing API'
)]
#[OA\Server(
    url: 'http://app.local',
    description: 'App local'
)]
#[OA\Tag(
    name: 'Tasks',
    description: 'Operations with asynchronous tasks'
)]
class OpenApiSpec
{
}

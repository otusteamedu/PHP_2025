<?php

declare(strict_types=1);

namespace App\Infrastructure\Http\Handler;

use App\Infrastructure\Http\Request\Request;
use App\Infrastructure\Http\Response\JsonResponse;

interface HandlerInterface
{
    public function handle(Request $request): JsonResponse;
}

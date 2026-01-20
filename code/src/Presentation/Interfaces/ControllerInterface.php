<?php

declare(strict_types=1);

namespace App\Presentation\Interfaces;

use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;

interface ControllerInterface
{
    public function handleRequest(Request $request): Response;

    public function run(): void;
}

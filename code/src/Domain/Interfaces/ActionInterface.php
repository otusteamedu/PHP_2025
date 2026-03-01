<?php

declare(strict_types=1);

namespace App\Domain\Interfaces;

use App\Infrastructure\Http\Request;
use App\Infrastructure\Http\Response;

interface ActionInterface
{
    public function supports(Request $request): bool;

    public function handle(Request $request): Response;
}

<?php
declare(strict_types=1);

namespace App\Application\Router;

use App\Application\Http\Request;
use App\Application\Http\Response;

interface RouteAction
{
    public function handle(Request $request): Response;
}

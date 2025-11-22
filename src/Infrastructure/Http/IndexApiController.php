<?php

namespace Blarkinov\Hw1500\Infrastructure\Http;

use Blarkinov\Hw1500\Domain\Controller\ControllerInterface;
use Blarkinov\Hw1500\Infrastructure\Route\AsRoute;

class IndexApiController implements ControllerInterface
{
    #[AsRoute(path: '/root')]
    public function index(): array
    {
        return [];
    }
}
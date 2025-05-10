<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Controller;

class HealthCheckAction extends BaseAction
{
    public function __invoke()
    {
        return $this->responseSuccess('ok')->asJson();
    }
}

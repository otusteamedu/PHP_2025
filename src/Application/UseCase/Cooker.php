<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use Closure;

final class Cooker extends AbstractCooker
{
    private Closure $closure;

    public function setClosureCook(Closure $closure): void
    {
        $this->closure = $closure;
    }

    protected function cook(): void
    {
        ($this->closure)();
    }
}

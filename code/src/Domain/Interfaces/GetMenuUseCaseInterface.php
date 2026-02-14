<?php

declare(strict_types=1);

namespace App\Domain\Interfaces;

interface GetMenuUseCaseInterface
{
    public function execute(): array;
}

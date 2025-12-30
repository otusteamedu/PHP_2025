<?php

declare(strict_types=1);

namespace Otus\Cache\Command;

readonly class RunCommand
{
    public function __invoke(): void
    {
        while (true) {
            sleep(1);
        }
    }
}

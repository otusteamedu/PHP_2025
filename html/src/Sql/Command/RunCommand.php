<?php

declare(strict_types=1);

namespace Otus\DataMapper\Sql\Command;

readonly class RunCommand
{
    public function __invoke(): void
    {
        while (true) {
            sleep(1);
        }
    }
}

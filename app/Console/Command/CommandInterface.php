<?php

declare(strict_types=1);

namespace App\Console\Command;

use App\Console\IO\ConsoleOutput;

interface CommandInterface
{
    public function getName(): string;

    public function getDescription(): string;

    /**
     * @param array<string, string|bool> $input
     */
    public function execute(array $input, ConsoleOutput $output): string;
}

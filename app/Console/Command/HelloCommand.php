<?php

declare(strict_types=1);

namespace App\Console\Command;

use App\Console\ConsoleApplication;
use App\Console\IO\ConsoleOutput;

final class HelloCommand implements CommandInterface
{
    public function getName(): string
    {
        return 'app:hello';
    }

    public function getDescription(): string
    {
        return 'Demo command that prints a greeting.';
    }

    public function execute(array $input, ConsoleOutput $output): string
    {
        $name = isset($input['name']) ? (string) $input['name'] : 'Developer';
        $message = sprintf('Hello, %s! Welcome to %s.', $name, ConsoleApplication::NAME);

        return $output->writeln($message);
    }
}

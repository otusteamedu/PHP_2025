<?php

declare(strict_types=1);

namespace App;

use RuntimeException;

final class App
{
    public function run(): void
    {
        $options = getopt('', ['action::', 'priority::', 'conditions::']);

        if (!isset($options['action'])) {
            throw new RuntimeException("Не передан параметр --action");
        }

        $redisConnect = new Connect;

        $command = new Command($redisConnect);

        try {
            $command->run($options);
        } catch (RuntimeException $e) {
            echo $e->getMessage();
        }
    }
}

<?php

declare(strict_types=1);

namespace App;

use App\Infrastructure\Console\Controller\ConsumerController;
use Exception;
use Throwable;

/**
 * Class ConsoleApplication
 * @package App
 */
class ConsoleApplication
{
    /**
     * @return void
     */
    public function run(): void
    {
        try {
            $this->handleRequest();
        } catch (Throwable $e) {
            print('Error: ' . $e->getMessage() . PHP_EOL);
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    private function handleRequest(): void
    {
        $this->runAction();
    }

    /**
     * @return void
     * @throws Exception
     */
    private function runAction(): void
    {
        (new ConsumerController())->actionConsume();
    }
}

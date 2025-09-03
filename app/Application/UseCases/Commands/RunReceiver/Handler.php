<?php
namespace App\Application\UseCases\Commands\RunReceiver;

use App\Infrastructure\Queue\RabbitmqWorker;

class Handler
{
    private $worker;

    public function __construct()
    {
        $this->worker = new RabbitmqWorker;
    }

    public function handle()
    {
        echo " [*] Waiting for messages. To exit press CTRL+C\n\n";
        $this->worker->runReceiver();
    }
}
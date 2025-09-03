<?php
namespace App\Application\UseCases\Commands\SendRequest;

use App\Infrastructure\Queue\RabbitmqWorker;

class Handler
{
    private $worker;

    public function __construct()
    {
        $this->worker = new RabbitmqWorker;
    }

    public function handle($request)
    {
        $this->worker->sendMessage($request);
    }
}
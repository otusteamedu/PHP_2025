<?php

namespace App\Infrastructure\Controllers;

use App\Application\UseCases\Commands\RunReceiver\Handler;

class ReceiverController
{
    private $runReceiverHandler;

    public function __construct()
    {
        $this->runReceiverHandler = new Handler();
    }

    public function runReceiver()
    {
        $this->runReceiverHandler->handle();
    }
}
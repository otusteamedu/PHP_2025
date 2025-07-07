<?php

namespace Domain\Receiver;

interface ReceiverInterface
{
    public function receive(callable $callback);
    public function getChannel();
}
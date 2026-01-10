<?php

namespace Consumer\Domain\Receiver;

interface ReceiverInterface
{
    public function receive(callable $callback);
}
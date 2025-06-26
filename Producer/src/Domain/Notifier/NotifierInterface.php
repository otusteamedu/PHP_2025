<?php

namespace Producer\Domain\Notifier;

interface NotifierInterface
{
    public function notify(string $message);
}
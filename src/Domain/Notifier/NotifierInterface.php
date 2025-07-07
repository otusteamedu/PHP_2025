<?php

namespace Domain\Notifier;

interface NotifierInterface
{
    public function notify(string $message);
}
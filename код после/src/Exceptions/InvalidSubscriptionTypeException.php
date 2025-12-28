<?php

namespace App\Exceptions;

class InvalidSubscriptionTypeException extends \Exception {
    public function __construct($message = "Invalid subscription type", $code = 0, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
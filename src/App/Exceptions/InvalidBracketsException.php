<?php
declare(strict_types=1);

namespace App\Exceptions;

class InvalidBracketsException extends \InvalidArgumentException
{
    public function __construct(string $message = 'Invalid brackets sequence')
    {
        parent::__construct($message);
    }
}
<?php
declare(strict_types=1);

namespace App\Exceptions;

class EmptyStringException extends \InvalidArgumentException
{
    public function __construct()
    {
        parent::__construct('String parameter is empty');
    }
}
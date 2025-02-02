<?php

declare(strict_types=1);

namespace Aovchinnikova\Hw15\Model;

class ValidationResult
{
    public $email;
    public $isValidFormat;
    public $hasValidDNS;

    public function __construct($email, $isValidFormat, $hasValidDNS)
    {
        $this->email = $email;
        $this->isValidFormat = $isValidFormat;
        $this->hasValidDNS = $hasValidDNS;
    }
}
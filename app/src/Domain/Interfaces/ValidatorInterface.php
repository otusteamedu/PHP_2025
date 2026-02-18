<?php

namespace Pryaniki\App\Domain\Interfaces;

interface ValidatorInterface
{

    public function validate(): bool;
}
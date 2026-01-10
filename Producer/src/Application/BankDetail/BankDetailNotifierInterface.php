<?php

namespace Producer\Application\BankDetail;

interface BankDetailNotifierInterface
{
    public function run(string $message);
}
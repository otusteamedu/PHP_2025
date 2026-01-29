<?php

namespace EmailsVerifier\Application\Interfaces;

interface VerifyEmailsUseCaseInterface
{
    public function execute(array $emailAddresses): array;
}

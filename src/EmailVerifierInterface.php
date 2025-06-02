<?php
namespace Elisad5791\Phpapp;

interface EmailVerifierInterface
{
    public function verifyEmail(string $email): array;
    public function verifyEmails(array $emails): string;
}
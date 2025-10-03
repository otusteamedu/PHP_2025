<?php
declare(strict_types=1);

namespace App\Validators;

final readonly class EmailValidator
{
    public function validate(string $email, ?bool $withMxRecord = false): bool
    {
         $formatIsValid = filter_var($email, FILTER_VALIDATE_EMAIL);
         if (!$formatIsValid) {
             return false;
         }

         if (!$withMxRecord) {
             return true;
         }

         $domain = explode('@', $email)[1];
         return $this->checkMxRecord($domain);
    }

    private function checkMxRecord(string $domain): bool
    {
        return getmxrr($domain, $hosts);
    }
}

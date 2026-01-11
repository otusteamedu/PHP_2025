<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Validators;

class ValidationEmail
{

    private ValidationService $validationService;

    public function setValidationService(ValidationService $validationService): void
    {
        $this->validationService = $validationService;
    }

    /**
     * @throws ValidationException
     */
    public function checkingEmailList(array $arEmails): array
    {
        $arValidationEmails = [];
        foreach ($arEmails as $email) {
            try {
                $this->validateEmail($email);
                $arValidationEmails[] = [
                    'email' => $email,
                    'result' => true,
                ];
            } catch (ValidationException $e) {
                $arValidationEmails[] = [
                    'email' => $email,
                    'result' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $arValidationEmails;
    }

    /**
     * @throws ValidationException
     */
    public function validateEmail(string $email): void
    {
        $email = $this->conversionsEmailToAscii($email);
        $this->validateEmailFormat($email);
        $this->validateDNS($email);
    }

    private function conversionsEmailToAscii(string $email): string
    {
        if ($this->validationService->isIDN()) {
            if (!str_contains($email, '@')) {
                $email =  $this->isIdnToAscii($email);
            } else {
                [$local, $domain] = explode('@', $email);

                $email =  implode('@', [
                    $this->isIdnToAscii($local),
                    $this->isIdnToAscii($domain)
                ]);
            }
        }

        return $email;
    }

    private function isIdnToAscii(string $idn): string
    {
        return (string) idn_to_ascii($idn, IDNA_NONTRANSITIONAL_TO_ASCII, INTL_IDNA_VARIANT_UTS46);
    }

    /**
     * @throws ValidationException
     */
    private function validateEmailFormat(string $email): void
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new ValidationException('Invalid format');
        }
    }

    /**
     * @throws ValidationException
     */
    private function validateDNS(string $email): void
    {
        if ($this->validationService->isRequiredValidateDNS()) {
            $domain = substr(strrchr($email, "@"), 1);

            if (empty($domain) || !(checkdnsrr($domain . '.'))) {
                throw new ValidationException('Invalid DNS');
            }
        }
    }
}
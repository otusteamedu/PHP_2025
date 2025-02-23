<?php

namespace App\Services;

use App\Validators\EmailValidator;

/**
 * Class EmailValidationService
 * @package App\Services
 */
class EmailValidationService
{
    /**
     * @var bool whether validation process should take into account IDN (internationalized domain names).
     */
    private bool $enableIDN;

    /**
     * @param bool $enableIDN
     */
    public function __construct(bool $enableIDN = false)
    {
        $this->enableIDN = $enableIDN;
    }

    /**
     * @param array $emails
     * @return array
     */
    public function validate(array $emails): array
    {
        $validator = new EmailValidator($this->enableIDN);
        $result = [];

        foreach ($emails as $email) {
            if (!$validator->validateFormat($email)) {
                $result[$email] = [
                    'result' => false,
                    'error' => 'Invalid format',
                ];
                continue;
            }

            if (!$validator->validateDnsMx($email)) {
                $result[$email] = [
                    'result' => false,
                    'error' => 'Invalid DNS MX',
                ];
                continue;
            }

            $result[$email] = [
                'result' => true,
            ];
        }

        return $result;
    }
}

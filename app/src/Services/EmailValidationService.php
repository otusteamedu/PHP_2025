<?php declare(strict_types=1);

namespace App\Services;

use App\Validators\EmailValidator;
use App\Validators\ValidationException;

/**
 * Class EmailValidationService
 * @package App\Services
 */
class EmailValidationService
{
    /**
     * @var bool
     */
    private bool $validateDNS;
    /**
     * @var bool whether validation process should take into account IDN (internationalized domain names).
     */
    private bool $enableIDN;

    /**
     * @param bool $validateDNS
     * @param bool $enableIDN
     */
    public function __construct(bool $validateDNS = false, bool $enableIDN = false)
    {
        $this->validateDNS = $validateDNS;
        $this->enableIDN = $enableIDN;
    }

    /**
     * @param array $emails
     * @return array
     */
    public function validate(array $emails): array
    {
        $validator = new EmailValidator($this->validateDNS, $this->enableIDN);
        $result = [];

        foreach ($emails as $email) {
            try {
                $validator->validate($email);
                $result[$email] = [
                    'result' => true,
                ];
            } catch (ValidationException $e) {
                $result[$email] = [
                    'result' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $result;
    }
}

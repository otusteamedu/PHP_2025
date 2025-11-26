<?php
declare(strict_types=1);

namespace App\Service;

use App\Exception\ValidationException;
use App\Validator\BracketValidator;

class VerificationService
{
    private BracketValidator $validator;

    public function __construct()
    {
        $this->validator = new BracketValidator();
    }

    /**
     * @param string $string
     * @return void
     * @throws ValidationException
     */
    public function validate(string $string): void
    {
        $this->validator->validate($string);
    }
}

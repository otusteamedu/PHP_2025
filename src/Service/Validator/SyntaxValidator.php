<?php
declare(strict_types=1);

namespace App\Service\Validator;

final class SyntaxValidator implements EmailValidatorInterface
{
    public function validate(string $email): bool
    {
        $atPos = strrpos($email, '@');

        if ($atPos === false) {
            return false;
        }

        $local = substr($email, 0, $atPos);
        $domain = substr($email, $atPos + 1);

        $asciiDomain = idn_to_ascii($domain,0);

        if ($asciiDomain === false) {
            return false;
        }

        return (bool) filter_var("$local@$asciiDomain", FILTER_VALIDATE_EMAIL);
    }

    public function getError(): array
    {
        return ['syntax_error' => 'Email не похож на настоящий'];
    }
}

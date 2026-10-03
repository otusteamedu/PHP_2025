<?php

declare(strict_types=1);

namespace App\Controller\Http\Web\BankReport\Rule;

use App\Domain\Shared\Validator\EmailFormatValidator;

class EmailRule implements RuleInterface
{
    public function __construct(
        // Используем валидатор из ДЗ №5
        private readonly EmailFormatValidator $emailFormatValidator,
    ) {
    }

    public function validate(array $data, array &$errors): void
    {
        $email = trim($data['email'] ?? '');

        if ($email === '') {
            $errors['email'][] = 'Поле "E-mail" обязательно для заполнения.';
            return;
        }

        if (!$this->emailFormatValidator->isValid($email)) {
            $errors['email'][] = 'Некорректный формат E-mail.';
        }
    }
}

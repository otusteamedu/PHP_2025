<?php

namespace Pryaniki\App\Presentation\Console;


use Pryaniki\App\Application\UseCases\ValidateEmailUseCase;

class ConsoleEmailValidatorRunner
{
    public function run(string $email): string
    {
        $useCase = new ValidateEmailUseCase();
        $resultDto = $useCase->execute($email);

        if (!$resultDto->success) {
            return "Email $email is invalid: {$resultDto->error}";
        } else {
            return "Email $email is valid";
        }
    }
}
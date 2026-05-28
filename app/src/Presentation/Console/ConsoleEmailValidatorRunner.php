<?php

namespace Pryaniki\App\Presentation\Console;

use Pryaniki\App\Application\UseCases\ValidateEmailUseCase;

class ConsoleEmailValidatorRunner
{
    public function run(string $email): string
    {
        $useCase = new ValidateEmailUseCase();

        $responseDto = $useCase->execute($email);

        if (!$responseDto->success) {
            return implode(PHP_EOL, $responseDto->error);
        } else {
            return "Email $email is valid";
        }
    }
}
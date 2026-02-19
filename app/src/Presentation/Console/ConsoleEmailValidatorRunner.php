<?php

namespace Pryaniki\App\Presentation\Console;


use Pryaniki\App\Application\UseCases\ValidateEmailUseCase;

class ConsoleEmailValidatorRunner
{
    public function run(string $email): string
    {
        $useCase = new ValidateEmailUseCase();
        return $useCase->execute($email);
    }
}
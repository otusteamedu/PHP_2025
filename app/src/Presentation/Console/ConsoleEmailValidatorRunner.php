<?php

namespace Pryaniki\App\Presentation\Console;


use Pryaniki\App\Application\UseCases\ValidateEmailUseCase;
use Pryaniki\App\Exceptions\NotExistDomainException;


class ConsoleEmailValidatorRunner
{
    public function run(string $email): string
    {
        $useCase = new ValidateEmailUseCase();

        try {
            $resultDto = $useCase->execute($email);
        } catch (NotExistDomainException $e) {
            return $e->getMessage();
        }

        if (!$resultDto->success) {
            return "Email $email is invalid: {$resultDto->error}";
        } else {
            return "Email $email is valid";
        }
    }
}
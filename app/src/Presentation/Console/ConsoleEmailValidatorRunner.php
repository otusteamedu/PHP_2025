<?php

namespace Pryaniki\App\Presentation\Console;

use Pryaniki\App\Application\UseCases\ValidateEmailUseCase;
use Pryaniki\App\Application\DTO\ValidateEmailRequestDTO;

class ConsoleEmailValidatorRunner
{
    public function run(string $email): string
    {
        $requestDto = new ValidateEmailRequestDTO($email);

        $useCase = new ValidateEmailUseCase($requestDto);

        $responseDto = $useCase->execute($email);

        if (!$responseDto->success) {
            return implode(PHP_EOL, $responseDto->error);
        } else {
            return "Email $email is valid";
        }
    }
}
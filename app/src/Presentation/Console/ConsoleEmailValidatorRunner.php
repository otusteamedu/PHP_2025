<?php

namespace Pryaniki\App\Presentation\Console;

use Pryaniki\App\Application\UseCases\ValidateEmailUseCase;
use Pryaniki\App\Domain\ValueObjects\Email\CompositeStringValidator;
use Pryaniki\App\Domain\ValueObjects\Email\Rules\EmailFormatRule;
use Pryaniki\App\Domain\ValueObjects\Email\Rules\NotEmptyRule;
use Pryaniki\App\Infrastructure\Services\DnsDomainChecker;

class ConsoleEmailValidatorRunner
{
    public function run(string $email): string
    {

        $validationRules = [
            new EmailFormatRule(),
            new NotEmptyRule()
        ];

        $useCase = new ValidateEmailUseCase(
            new CompositeStringValidator($validationRules),
            new DnsDomainChecker()
        );

        $responseDto = $useCase->execute($email);

        if (!$responseDto->success) {
            return implode(PHP_EOL, $responseDto->error);
        } else {
            return "Email $email is valid";
        }
    }
}
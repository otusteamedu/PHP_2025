<?php

declare(strict_types=1);

namespace App\Infrastructure\Mail\Exception;

use App\Infrastructure\Mail\Mailpit\MailpitChaosTrigger;
use App\Infrastructure\Mail\Smtp\SmtpCode;

class MailpitChaosException extends \RuntimeException
{
    public static function configurationFailed(\Throwable $e): self
    {
        return new self('Mailpit chaos: failed to configure', previous: $e);
    }

    public static function unexpectedStatus(int $statusCode): self
    {
        return new self(sprintf('Mailpit chaos: API returned %d', $statusCode));
    }

    public static function missingTrigger(MailpitChaosTrigger $trigger): self
    {
        return new self(sprintf('Mailpit chaos: trigger "%s" missing in response', $trigger->value));
    }

    public static function settingsNotApplied(
        MailpitChaosTrigger $trigger,
        SmtpCode $expectedCode,
        int $expectedProbability,
        array $actual,
    ): self {
        return new self(sprintf(
            'Mailpit chaos: expected %s = {code: %d, probability: %d}, got {code: %s, probability: %s}',
            $trigger->value,
            $expectedCode->value,
            $expectedProbability,
            $actual['ErrorCode'] ?? 'null',
            $actual['Probability'] ?? 'null',
        ));
    }
}

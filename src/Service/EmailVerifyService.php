<?php
declare(strict_types=1);

namespace App\Service;

use App\Service\Dto\VerificationResponse;
use App\Service\Validator\EmailValidatorInterface;
use App\Service\Validator\MxValidator;
use App\Service\Validator\SyntaxValidator;
use Generator;

final class EmailVerifyService
{
    /** @var EmailValidatorInterface[] */
    private array $validators;

    public function __construct(?array $validators = null)
    {
        $this->validators = [
            new SyntaxValidator(),
            new MxValidator(),
        ];
    }

    /**
     * @param string[] $emails
     * @return VerificationResponse[]
     */
    public function verifyBatch(array $emails): array
    {
        $results = [];
        foreach ($emails as $email) {
            $results[] = $this->verify((string)$email);
        }

        return $results;
    }

    /**
     * @param iterable<string> $emails
     * @param int $batchSize
     * @return Generator<VerificationResponse[]>
     */
    public function verifyLargeList(iterable $emails, int $batchSize = 100): Generator
    {
        $buffer = [];

        foreach ($emails as $input) {
            $buffer[] = (string)$input;

            if (count($buffer) >= $batchSize) {
                yield $this->verifyBatch($buffer);
                $buffer = [];
            }
        }

        if (!empty($buffer)) {
            yield $this->verifyBatch($buffer);
        }
    }

    public function verify(string $email): VerificationResponse
    {
        $cleanEmail = trim($email);

        foreach ($this->validators as $validator) {
            if (!$validator->validate($cleanEmail)) {
                return new VerificationResponse(
                    $email,
                    false,
                    $validator->getError()
                );
            }
        }

        return new VerificationResponse($email, true);
    }
}

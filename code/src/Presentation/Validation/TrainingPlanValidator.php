<?php

declare(strict_types=1);

namespace App\Presentation\Validation;

use Assert\Assertion;
use Assert\AssertionFailedException;

class TrainingPlanValidator
{
    /**
     * @throws ValidationException
     */
    public function validate(array $data): void
    {
        try {
            Assertion::keyExists($data, 'name', 'Name is required.');
            Assertion::string($data['name'], 'Name must be a string.');
            Assertion::notEmpty($data['name'], 'Name cannot be empty.');

            if (isset($data['description'])) {
                Assertion::string($data['description'], 'Description must be a string.');
            }
        } catch (AssertionFailedException $e) {
            throw new ValidationException([$e->getMessage()]);
        }
    }
}

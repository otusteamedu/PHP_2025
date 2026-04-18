<?php

declare(strict_types=1);

namespace App\Presentation\Validation;

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

class UserValidator
{
    /**
     * @throws ValidationException
     */
    public function validate(array $data): void
    {
        $validator = v::key('email', v::email())
            ->key('name', v::stringType()->notEmpty())
            ->key('born', v::date())
            ->key('gender', v::in(['male', 'female']))
            ->key('weight', v::numericVal()->positive())
            ->key('height', v::numericVal()->positive());

        try {
            $validator->assert($data);
        } catch (NestedValidationException $exception) {
            throw new ValidationException($exception->getMessages());
        }
    }

    /**
     * @throws ValidationException
     */
    public function validateUpdate(array $data): void
    {
        $validator = v::key('email', v::email(), false)
            ->key('name', v::stringType()->notEmpty(), false)
            ->key('born', v::date(), false)
            ->key('gender', v::in(['male', 'female']), false)
            ->key('weight', v::numericVal()->positive(), false)
            ->key('height', v::numericVal()->positive(), false);

        try {
            $validator->assert($data);
        } catch (NestedValidationException $exception) {
            throw new ValidationException($exception->getMessages());
        }
    }
}

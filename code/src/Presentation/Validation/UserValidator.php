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
            ->key('name', v::stringVal()->notEmpty())
            ->key('born', v::date()->notEmpty())
            ->key('gender', v::in(['male', 'female']))
            ->key('weight', v::numericVal()->positive())
            ->key('height', v::numericVal()->positive());

        try {
            $validator->assert($data);
        } catch (NestedValidationException $exception) {
            throw new ValidationException($exception->getMessages());
        }
    }
}

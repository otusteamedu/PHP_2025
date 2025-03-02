<?php
declare(strict_types=1);


namespace App\Validator;

use App\Validator\VO\Error;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\ValidatorBuilder;

readonly class Validator
{
    private ValidatorBuilder $validatorBuilder;

    public function __construct()
    {
        $this->validatorBuilder = new ValidatorBuilder();
    }

    /**
     * @return Error[]
     */
    public function validate(array $request, Collection $constraint): array
    {
        $violations = $this->validatorBuilder->getValidator()->validate($request, $constraint);
        $errors = [];
        foreach ($violations as $violation) {
            $errors[] = new Error($violation->getPropertyPath(), $violation->getMessage());
        }

        return $errors;
    }

    public function hasEmailMxRecord(string $email): bool
    {
        return !empty(dns_get_record(explode('@', $email)[1], DNS_MX));
    }
}
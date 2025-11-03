<?php

namespace App\Shared\Validate;

class ValidateRequestMethodPost
{
    private const string METHOD_POST = 'POST';
    public static function validate(): ?string
    {
        $requestMethod = new RequestMethod();
        $requestMethodValidate = $requestMethod->validate(self::METHOD_POST);
        if ($requestMethodValidate !== null) {
            return $requestMethodValidate;
        }

        return null;
    }

}
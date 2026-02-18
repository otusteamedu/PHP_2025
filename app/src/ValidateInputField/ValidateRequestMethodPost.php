<?php

declare(strict_types=1);

namespace User\Php2025\src\ValidateInputField;

use User\Php2025\src\Validate\RequestMethod;

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
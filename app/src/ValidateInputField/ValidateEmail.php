<?php

declare(strict_types=1);

namespace User\Php2025\src\ValidateInputField;

use User\Php2025\src\Validate\MaxLength;
use User\Php2025\src\Validate\MinLength;
use User\Php2025\src\Validate\NotBlank;
use User\Php2025\src\Validate\StringType;
use User\Php2025\src\Validate\ValidationEmail;

class ValidateEmail
{
    public function validate(mixed $email): ?string
    {
        $notBlank = new NotBlank();
        $notBlankValidate = $notBlank->validate($email);
        if ($notBlankValidate !== null) {
            return $notBlankValidate;
        }

        $stringType = new StringType();
        $stringTypeValidate = $stringType->validate($email);
        if($stringTypeValidate !== null) {
            return $stringTypeValidate;
        }

        $maxLength = new MaxLength();
        $maxLengthValidate = $maxLength->validate($email, 245);
        if($maxLengthValidate !== null) {
            return $maxLengthValidate;
        }

        $minLength = new MinLength();
        $minLengthValidate = $minLength->validate($email, 5);
        if($minLengthValidate !== null) {
            return $minLengthValidate;
        }

        $validateEmail = new ValidationEmail();
        $validateEmailResult = $validateEmail->validate($email);
        if($validateEmailResult !== null) {
            return $validateEmailResult;
        }

        return null;
    }
}

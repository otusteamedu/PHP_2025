<?php

namespace Pryaniki\App\Exceptions\Validation\Field;


class NotValidException extends FieldException
{
    protected function getMessageText(): string
    {
        return "Field $this->fieldName is not valid";
    }
}
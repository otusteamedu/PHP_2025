<?php

namespace Pryaniki\App\Exceptions\Validation\Field;

class EmptyFieldException extends FieldException
{
    protected function getMessageText(): string
    {
        return "Field $this->fieldName is empty";
    }
}
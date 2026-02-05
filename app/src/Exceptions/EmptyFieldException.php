<?php

namespace Pryaniki\App\Exceptions;

class EmptyFieldException extends FieldException
{
    protected function getMessageText(): string
    {
        return "Field $this->fieldName is empty";
    }
}
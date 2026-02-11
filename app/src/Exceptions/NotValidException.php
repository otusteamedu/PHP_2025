<?php

namespace Pryaniki\App\Exceptions;

class NotValidException extends FieldException
{
    protected function getMessageText(): string
    {
        return "Field $this->fieldName is not valid";
    }
}
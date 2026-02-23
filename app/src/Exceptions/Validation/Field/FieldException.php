<?php

namespace Pryaniki\App\Exceptions\Validation\Field;

use Pryaniki\App\Exceptions\Exception;

abstract class FieldException extends Exception
{
    protected string $fieldName;
    public function __construct($fieldName, $code = 0, ?Throwable $previous = null)
    {
        $this->fieldName = $fieldName ?: '';
        parent::__construct($this->getMessageText(), $code, $previous);
    }
   abstract protected function getMessageText(): string;

}
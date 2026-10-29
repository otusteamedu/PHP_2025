<?php

declare(strict_types=1);

namespace App\Controller\Http\Api\Shared\Validation;

use App\Core\Http\Exception\FieldValidationException;
use Customer41\MultiException\MultiException;

class ValidationFailedException extends MultiException
{
    public function getErrorsByField(): array
    {
        $grouped = [];

        foreach ($this as $error) {
            if ($error instanceof FieldValidationException) {
                $grouped[$error->getField()][] = $error->getMessage();
            }
        }

        return $grouped;
    }

    /**
     * @throws self
     */
    public function throwIfAny(): void
    {
        if ($this->hasErrors()) {
            throw $this;
        }
    }
}

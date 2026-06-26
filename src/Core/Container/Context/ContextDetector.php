<?php

declare(strict_types=1);

namespace App\Core\Container\Context;

class ContextDetector
{
    private ?ContextType $contextType = null;

    public function getContextType(): ContextType
    {
        if ($this->contextType === null) {
            $this->contextType = $this->detectContextType();
        }

        return $this->contextType;
    }

    private function detectContextType(): ContextType
    {
        if (PHP_SAPI === ContextType::CLI->value) {
            return ContextType::CLI;
        }

        return str_starts_with($_SERVER['REQUEST_URI'], '/api/')
            ? ContextType::HTTP_API
            : ContextType::HTTP_WEB;
    }
}

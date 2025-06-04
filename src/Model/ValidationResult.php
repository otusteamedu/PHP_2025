<?php
declare(strict_types=1);

namespace Aovchinnikova\Hw15\Model;

class ValidationResult
{
    private $email;
    private $isValidFormat;
    private $hasValidDNS;

    public function __construct(string $email, bool $isValidFormat, bool $hasValidDNS)
    {
        $this->email = $email;
        $this->isValidFormat = $isValidFormat;
        $this->hasValidDNS = $hasValidDNS;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function isValidFormat(): bool
    {
        return $this->isValidFormat;
    }

    public function hasValidDNS(): bool
    {
        return $this->hasValidDNS;
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'isValid' => $this->isValidFormat && $this->hasValidDNS,
            'details' => [
                'format' => $this->isValidFormat,
                'dns' => $this->hasValidDNS
            ]
        ];
    }
}

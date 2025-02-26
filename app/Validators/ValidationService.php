<?php
declare(strict_types=1);

namespace Zibrov\OtusPhp2025\Validators;

class ValidationService
{

    private bool $isRequiredValidateDNS= false;
    private bool $isIDN = false;

    public function setIsRequiredValidateDNS(bool $isRequiredValidateDNS = false): void
    {
        $this->isRequiredValidateDNS = $isRequiredValidateDNS;
    }

    public function setIsIDN(bool $isIDN = false): void
    {
        $this->isIDN = $isIDN;
    }

    public function isRequiredValidateDNS(): bool
    {
        return $this->isRequiredValidateDNS;
    }

    public function isIDN(): bool
    {
        return $this->isIDN;
    }
}
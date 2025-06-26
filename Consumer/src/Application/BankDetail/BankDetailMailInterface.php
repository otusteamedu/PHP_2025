<?php

namespace Consumer\Application\BankDetail;

use Consumer\Domain\DTO\BankDetailDTO;

interface BankDetailMailInterface
{
    public function mail(
        BankDetailDTO $dto,
        string $subject,
        string $to,
        ?string $from
    ): void;
}
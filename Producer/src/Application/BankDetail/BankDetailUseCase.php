<?php

namespace Producer\Application\BankDetail;

use Producer\Domain\DTO\BankDetailDTO;

class BankDetailUseCase
{
    public function __construct(
        protected BankDetailNotifierInterface $bankDetailNotifier,
    ) {
    }

    public function run(BankDetailDTO $dto): void {
        $message = json_encode([
            'bik' => $dto->bik,
            'account' => $dto->account,
            'client' => $dto->client,
            'bank' => $dto->bank,
        ]);

        $this->bankDetailNotifier->run($message);
    }
}
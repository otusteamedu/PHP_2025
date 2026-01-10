<?php

namespace Producer\Domain\DTO;

class BankDetailDTO
{
    public string $bik;
    public string $account;
    public string $client;
    public string $bank;

    public function __construct(
        string $bik,
        string $account,
        string $client,
        string $bank
    ) {
        $this->bik = $bik;
        $this->account = $account;
        $this->client = $client;
        $this->bank = $bank;
    }
}
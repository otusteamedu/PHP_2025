<?php

namespace Blarkinov\RabbitMq\Models;

use JsonSerializable;

class BankStatement implements JsonSerializable
{

    public const TRANSACTION_TYPE_TRANSFER = 'TRANSFER';
    public const TRANSACTION_TYPE_DEPOSIT = 'DEPOSIT';
    public const TRANSACTION_TYPE_PAYMENT = 'PAYMENT';

    private ?string $id;
    private string $date;
    private string $description;
    private string $amount;
    private string $balance;
    private string $transactionType;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        $this->date = $data['date'];
        $this->description = $data['description'];
        $this->amount = $data['amount'];
        $this->balance = $data['balance'];
        $this->transactionType = $data['transactionType'];
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'description' => $this->description,
            'amount' => $this->amount,
            'balance' => $this->balance,
            'transactionType' => $this->transactionType,
        ];
    }

    public function getId(): ?string
    {
        return $this->id;
    }
    public function getDate(): string
    {
        return $this->date;
    }
    public function getDescription(): string
    {
        return $this->description;
    }
    public function getAmount(): string
    {
        return $this->amount;
    }
    public function getBalance(): string
    {
        return $this->balance;
    }
    public function getTransactionType(): string
    {
        return $this->transactionType;
    }
}

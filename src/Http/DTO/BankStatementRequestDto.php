<?php

namespace Blarkinov\RabbitMq\Http\DTO;

use Blarkinov\RabbitMq\Models\BankStatement;
use DateTime;
use Exception;
use JsonSerializable;

class BankStatementRequestDto implements JsonSerializable
{

    private const RANGE_PATTERN = '/\d{4}-\d{2}-\d{2}/';

    public function __construct(
        private ?string $dateFrom,
        private ?string $dateTo,
        private ?string $transactionType,
    ) {

        $this->dateFrom = $this->validateRange($dateFrom);
        $this->dateTo = $this->validateRange($dateTo);
        $this->transactionType =  $this->validateTransactionType($transactionType);
    }

    public function transactionType(): ?string
    {
        return $this->dateFrom;
    }
    public function getDateFrom(): ?string
    {
        return $this->dateFrom;
    }
    public function getDateTo(): ?string
    {
        return $this->dateTo;
    }
    public function getTransactionType(): ?string
    {
        return $this->transactionType;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
            'transactionType' => $this->transactionType,
        ];
    }

    private function validateRange(string $date)
    {
        if (is_null($date))
            return null;

        if (!preg_match(self::RANGE_PATTERN, $date, $match))
            throw new Exception('invalid date value');

        if (DateTime::createFromFormat('Y-m-d', $date) === false)
            throw new Exception('invalid date value');

        return $date;
    }

    private function validateTransactionType(string $type)
    {
        if (is_null($type))
            return null;
        if (!in_array($type, [BankStatement::TRANSACTION_TYPE_DEPOSIT, BankStatement::TRANSACTION_TYPE_PAYMENT, BankStatement::TRANSACTION_TYPE_TRANSFER]))
            throw new Exception('invalid type transaction');

        return $type;
    }
}

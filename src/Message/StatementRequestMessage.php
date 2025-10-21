<?php

namespace App\Message;

class StatementRequestMessage
{
    public function __construct(
        private string $email,
        private string $accountNumber,
        private string $startDate,
        private string $endDate,
        private string $requestId
    ) {}

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    public function getStartDate(): string
    {
        return $this->startDate;
    }

    public function getEndDate(): string
    {
        return $this->endDate;
    }

    public function getRequestId(): string
    {
        return $this->requestId;
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'accountNumber' => $this->accountNumber,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'requestId' => $this->requestId,
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['email'],
            $data['accountNumber'],
            $data['startDate'],
            $data['endDate'],
            $data['requestId']
        );
    }
}
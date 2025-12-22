<?php

namespace Blarkinov\RabbitMq\Models;

use Blarkinov\RabbitMq\Http\DTO\BankStatementRequestDto;
use DateTime;
use JsonSerializable;

class BankStatementCollection implements JsonSerializable
{
    private array $list = [];

    public function add(BankStatement $bankStatement): BankStatementCollection
    {
        $this->list[] = $bankStatement;
        return $this;
    }

    public function filter(BankStatementRequestDto $dto): BankStatementCollection
    {
        $filterBankStatements = $this->list;

        if ($dto->getDateTo()) {
            $filterBankStatements = array_filter(
                $filterBankStatements,
                function ($item) use ($dto) {
                    if ($item instanceof BankStatement)
                        return new DateTime($item->getDate()) <= new DateTime($dto->getDateTo());
                }
            );
        }
        if ($dto->getDateFrom()) {
            $filterBankStatements = array_filter(
                $filterBankStatements,
                function ($item) use ($dto) {
                    if ($item instanceof BankStatement)
                        return new DateTime($item->getDate()) >= new DateTime($dto->getDateFrom());
                }
            );
        }
        if ($dto->getTransactionType()) {
            $filterBankStatements = array_filter(
                $filterBankStatements,
                function ($item) use ($dto) {
                    if ($item instanceof BankStatement)
                        return $item->getTransactionType() === $dto->getTransactionType();
                }
            );
        }

        $newCollection = new $this;

        foreach ($filterBankStatements as $item) {
            if ($item instanceof BankStatement)
                $newCollection->add($item);
        }
        return $newCollection;
    }

    public function isEmpty(): bool
    {
        return empty($this->list);
    }

    public function jsonSerialize(): mixed
    {
        return $this->list;
    }
}

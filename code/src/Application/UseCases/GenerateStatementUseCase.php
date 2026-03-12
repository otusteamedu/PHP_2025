<?php

declare(strict_types=1);

namespace Queues\Application\UseCases;

use Queues\Domain\Entities\Statement;

class GenerateStatementUseCase
{
    private const DESCRIPTIONS = [
        'Пополнение счёта',
        'Снятие наличных',
        'Перевод на карту',
        'Оплата в магазине',
        'Комиссия банка',
    ];

    public function execute(Statement $statement): Statement
    {
        return Statement::withContent($statement, $this->generateContent($statement));
    }

    private function generateContent(Statement $statement): string
    {
        return <<<STATEMENT

БАНКОВСКАЯ ВЫПИСКА
==================

Период: {$statement->dateFrom} - {$statement->dateTo}",
Дата запроса: {$statement->requestDt}",

ТРАНЗАКЦИИ:
...
Бла Бла Бла...
...
Итого операций: xxx

STATEMENT;
    }
}

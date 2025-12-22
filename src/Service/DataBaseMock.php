<?php

namespace Blarkinov\RabbitMq\Service;

use Blarkinov\RabbitMq\Models\BankStatement;
use Blarkinov\RabbitMq\Models\BankStatementCollection;

class DataBaseMock
{
    public static function getBankStatementTable(): BankStatementCollection
    {
        $data = [
            [
                'id' => 'tx1',
                'date' => '2024-02-04',
                'description' => 'Оплата по счету',
                'amount' => '150.00',
                'balance' => '1000.00',
                'transactionType' => BankStatement::TRANSACTION_TYPE_PAYMENT
            ],
            [
                'id' => 'tx2',
                'date' => '2024-07-22',
                'description' => 'Депозит на карту',
                'amount' => '200.00',
                'balance' => '1200.00',
                'transactionType' => BankStatement::TRANSACTION_TYPE_DEPOSIT
            ],
            [
                'id' => 'tx3',
                'date' => '2024-01-03',
                'description' => 'Перевод другу',
                'amount' => '-50.00',
                'balance' => '1150.00',
                'transactionType' => BankStatement::TRANSACTION_TYPE_TRANSFER
            ],
            [
                'id' => 'tx4',
                'date' => '2024-07-14',
                'description' => 'Оплата ЖКХ',
                'amount' => '-300.00',
                'balance' => '850.00',
                'transactionType' => BankStatement::TRANSACTION_TYPE_PAYMENT
            ],
            [
                'id' => 'tx5',
                'date' => '2024-11-05',
                'description' => 'Поступление зарплаты',
                'amount' => '500.00',
                'balance' => '1350.00',
                'transactionType' => BankStatement::TRANSACTION_TYPE_DEPOSIT
            ],
            [
                'id' => 'tx6',
                'date' => '2024-07-06',
                'description' => 'Перевод на карту',
                'amount' => '-75.00',
                'balance' => '1275.00',
                'transactionType' => BankStatement::TRANSACTION_TYPE_TRANSFER
            ],
            [
                'id' => 'tx7',
                'date' => '2024-09-17',
                'description' => 'Оплата мобильного',
                'amount' => '-30.00',
                'balance' => '1245.00',
                'transactionType' => BankStatement::TRANSACTION_TYPE_PAYMENT
            ],
            [
                'id' => 'tx8',
                'date' => '2024-01-08',
                'description' => 'Лотерея выигрыш',
                'amount' => '1000.00',
                'balance' => '2245.00',
                'transactionType' => BankStatement::TRANSACTION_TYPE_DEPOSIT
            ],
            [
                'id' => 'tx9',
                'date' => '2024-02-09',
                'description' => 'Покупка мебели',
                'amount' => '-800.00',
                'balance' => '1445.00',
                'transactionType' => BankStatement::TRANSACTION_TYPE_PAYMENT
            ],
            [
                'id' => 'tx10',
                'date' => '2024-01-10',
                'description' => 'Подарок другу',
                'amount' => '-200.00',
                'balance' => '1245.00',
                'transactionType' => BankStatement::TRANSACTION_TYPE_TRANSFER
            ],
            [
                'id' => 'tx11',
                'date' => '2024-01-11',
                'description' => 'Подарок маме',
                'amount' => '-150.00',
                'balance' => '1095.00',
                'transactionType' => BankStatement::TRANSACTION_TYPE_TRANSFER
            ],
            [
                'id' => 'tx12',
                'date' => '2024-01-12',
                'description' => 'Плата за интернет',
                'amount' => '-40.00',
                'balance' => '1055.00',
                'transactionType' => BankStatement::TRANSACTION_TYPE_PAYMENT
            ],
            [
                'id' => 'tx13',
                'date' => '2024-01-13',
                'description' => 'Поступление дивидендов',
                'amount' => '250.00',
                'balance' => '1305.00',
                'transactionType' => BankStatement::TRANSACTION_TYPE_DEPOSIT
            ],
            [
                'id' => 'tx14',
                'date' => '2024-02-14',
                'description' => 'Купи книгу',
                'amount' => '-20.00',
                'balance' => '1285.00',
                'transactionType' => BankStatement::TRANSACTION_TYPE_PAYMENT
            ],
            [
                'id' => 'tx15',
                'date' => '2024-09-15',
                'description' => 'Погашение кредита',
                'amount' => '-500.00',
                'balance' => '785.00',
                'transactionType' => BankStatement::TRANSACTION_TYPE_PAYMENT
            ],
            [
                'id' => 'tx16',
                'date' => '2024-01-16',
                'description' => 'Перевод за границу',
                'amount' => '-100.00',
                'balance' => '685.00',
                'transactionType' => BankStatement::TRANSACTION_TYPE_TRANSFER
            ],
            [
                'id' => 'tx17',
                'date' => '2024-10-17',
                'description' => 'Зачисление за работу',
                'amount' => '600.00',
                'balance' => '1285.00',
                'transactionType' => BankStatement::TRANSACTION_TYPE_DEPOSIT
            ],
            [
                'id' => 'tx18',
                'date' => '2024-01-18',
                'description' => 'Покупка одежды',
                'amount' => '-120.00',
                'balance' => '1165.00',
                'transactionType' => BankStatement::TRANSACTION_TYPE_PAYMENT
            ],
            [
                'id' => 'tx19',
                'date' => '2024-11-19',
                'description' => 'Оплата за обучение',
                'amount' => '-300.00',
                'balance' => '865.00',
                'transactionType' => BankStatement::TRANSACTION_TYPE_PAYMENT
            ],
            [
                'id' => 'tx20',
                'date' => '2024-01-20',
                'description' => 'Бонус за работу',
                'amount' => '100.00',
                'balance' => '965.00',
                'transactionType' => BankStatement::TRANSACTION_TYPE_DEPOSIT
            ],
        ];

        $statementCollection = new BankStatementCollection;

        foreach ($data as $item) {
            $statementCollection->add(new BankStatement($item));
        }

        return $statementCollection;
    }
}

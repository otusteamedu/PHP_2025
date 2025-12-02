<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Domain\Statement\Repository;

use Dinargab\Homework20\Domain\Statement\Entity\BankStatement;

interface BankStatementRepositoryInterface
{
    public function addStatement(BankStatement $statement): BankStatement;

    public function getAllStatements(): array;

    public function getStatementById(int $id): BankStatement;

}
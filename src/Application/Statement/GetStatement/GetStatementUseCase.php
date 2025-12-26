<?php
declare(strict_types=1);

namespace Dinargab\Homework20\Application\Statement\GetStatement;

use Dinargab\Homework20\Domain\Exception\EntityNotFoundException;
use Dinargab\Homework20\Domain\Statement\Repository\BankStatementRepositoryInterface;

class GetStatementUseCase
{
    public function __construct(
        private readonly BankStatementRepositoryInterface $statementRepository,
    )
    {

    }

    public function __invoke(GetStatementRequest $request) : ?GetStatementResponse
    {
        $statement = $this->statementRepository->getStatementById($request->getStatementId());
        if ($statement === null) {
            throw new EntityNotFoundException("Statement with id {$request->getStatementId()} not found");
        }
        return new GetStatementResponse(
            $statement->getId(),
            (string) $statement->getDateFrom(),
            (string) $statement->getDateTo(),
        );
    }
}
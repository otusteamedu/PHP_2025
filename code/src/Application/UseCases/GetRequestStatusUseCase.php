<?php

declare(strict_types=1);

namespace Api\Application\UseCases;

use Api\Domain\Interfaces\RepositoryInterface;

final class GetRequestStatusUseCase
{
    private const FORMAT = 'Y-m-d H:i:s';

    public function __construct(
        private RepositoryInterface $repository
    ) {
    }

    public function execute(int $id): ?array
    {
        $request = $this->repository->getById($id);

        if ($request === null) {
            return null;
        }

        return [
            'id' => $request->id,
            'status' => $request->status->value,
            'content' => $request->content,
            'created' => $request->created->format(self::FORMAT),
            'processed' => $request->processed?->format(self::FORMAT),
            'result' => $request->result,
        ];
    }
}

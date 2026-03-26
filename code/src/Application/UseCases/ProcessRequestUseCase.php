<?php

declare(strict_types=1);

namespace Api\Application\UseCases;

use Api\Domain\Enums\RequestStatus;
use Api\Domain\Interfaces\RepositoryInterface;

final class ProcessRequestUseCase
{
    public function __construct(
        private RepositoryInterface $repository
    ) {
    }

    public function execute(int $id): bool
    {
        $request = $this->repository->getById($id);

        if ($request === null) {
            return false;
        }

        $request = $request->setStatus(status: RequestStatus::PROCESSING);
        $this->repository->updateStatus($id, $request);

        $processedDt = new \DateTimeImmutable();

        try {
            if (random_int(1, 5) === 1) {
                throw new \Exception('Имитация случайной ошибки');
            }

            $result = $this->processContent($request->content);

            $request = $request->setStatus(
                status: RequestStatus::COMPLETED,
                result: $result,
                processed: $processedDt
            );
            $this->repository->updateStatus($id, $request);

            return true;
        } catch (\Throwable $e) {
            $request = $request->setStatus(
                status: RequestStatus::FAILED,
                result: 'Ошибка: ' . $e->getMessage(),
                processed: $processedDt
            );
            $this->repository->updateStatus($id, $request);

            return false;
        }
    }

    private function processContent(string $content): string
    {
        return 'ОБРАБОТАНО: ' . $content;
    }
}

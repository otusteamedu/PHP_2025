<?php

declare(strict_types=1);

namespace Dinargab\Homework14\Application\UseCase;

use Dinargab\Homework14\Application\DataProvider\BookDataProviderInterface;
use Dinargab\Homework14\Domain\Repository\BookSearchRepositoryInterface;
use Dinargab\Homework14\Infrastructure\Factory\BookFactory;

class ImportBooksUseCase
{
    private const BATCH_SIZE = 1000;

    public function __construct(
        private BookDataProviderInterface $bookDataProvider,
        private BookSearchRepositoryInterface $bookSearchRepository,
        private BookFactory $bookFactory,
    ) {
    }

    public function __invoke(
    ): void {
        $this->bookSearchRepository->clear();

        $batch = [];

        //Перекладываем Jsonы ...
        foreach ($this->bookDataProvider->load() as $bookDto) {
            $batch[] = $this->bookFactory->createFromDTO($bookDto);

            if (count($batch) >= self::BATCH_SIZE) {
                $this->bookSearchRepository->bulkSave($batch);
                $batch = [];
            }
        }

        // Сохраняем остатки
        if (!empty($batch)) {
            $this->bookSearchRepository->bulkSave($batch);
        }
    }
}
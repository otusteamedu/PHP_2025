<?php declare(strict_types=1);

namespace App\Application\UseCase\FindAllNews;

use App\Domain\Repository\NewsRepositoryInterface;

class FindAllNewsUseCase
{
    public function __construct(
        private readonly NewsRepositoryInterface $newsRepository
    )
    {
    }

    /**
     * @return \Generator|FindAllNewsResponse[]
     */
    public function __invoke(): iterable
    {
        foreach ($this->newsRepository->findAll() as $news) {
            yield new FindAllNewsResponse(
                $news->getId(),
                $news->getUrl()->getValue(),
                $news->getTitle()->getValue(),
                $news->getCreatedAt()->format('Y-m-d H:i:s')
            );
        }
    }
}

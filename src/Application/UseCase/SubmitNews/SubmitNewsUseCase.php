<?php declare(strict_types=1);

namespace App\Application\UseCase\SubmitNews;

use App\Domain\Factory\NewsFactoryInterface;
use App\Domain\Repository\NewsRepositoryInterface;

class SubmitNewsUseCase
{
    public function __construct(
        private readonly NewsFactoryInterface $newsFactory,
        private readonly NewsRepositoryInterface $newsRepository,
    )
    {
    }

    public function __invoke(SubmitNewsRequest $request): SubmitNewsResponse
    {
        $news = $this->newsFactory->create($request->url, $request->title);
        $this->newsRepository->save($news);

        return new SubmitNewsResponse(
            $news->getId(),
        );
    }
}

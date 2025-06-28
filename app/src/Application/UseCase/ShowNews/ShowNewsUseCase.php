<?php

declare(strict_types=1);

namespace App\Application\UseCase\ShowNews;

use App\Application\NewsContentModifier\DummyNewsContentModifier;
use App\Application\NewsContentModifier\NewsContentModifierInterface;
use App\Domain\Repository\NewsRepositoryInterface;
use App\Domain\Repository\NotFoundException;

readonly class ShowNewsUseCase
{
    public function __construct(
        private NewsRepositoryInterface $newsRepository,
    )
    {
    }

    public function __invoke(
        ShowNewsRequest $request,
        NewsContentModifierInterface $newsContentModifier = new DummyNewsContentModifier()
    ): ShowNewsResponse
    {
        $news = $this->newsRepository->findOneById($request->id);

        if (empty($news)) {
            throw new NotFoundException('News not found');
        }

        return new ShowNewsResponse(
            $news->getId(),
            $news->getTitle()->getValue(),
            $news->getAuthor()->getValue(),
            $news->getCategory()->getName()->getValue(),
            $newsContentModifier->modify($news->getContent()->getValue()),
            $news->getCreatedAt()->format('Y-m-d H:i:s'),
        );
    }
}
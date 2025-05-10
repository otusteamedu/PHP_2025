<?php

declare(strict_types=1);

namespace App\News\Application\UseCase\MakeNews;

use App\News\Application\GateWay\NewsParserInterface;
use App\News\Domain\Factory\NewsFactoryInterface;
use App\News\Domain\Repository\NewsRepositoryInterface;

readonly class MakeNewsUseCase
{
    public function __construct(
        private NewsRepositoryInterface $repository,
        private NewsFactoryInterface    $newsFactory,
        private NewsParserInterface     $newsParser,
    )
    {
    }

    public function __invoke(MakeNewsRequest $request): MakeNewsResponse
    {
        $title = $this->newsParser->getTitle($request->link);
        $news = $this->newsFactory->create($title, $request->link);
        $this->repository->save($news);

        return new MakeNewsResponse($news->getId()->toString());
    }

}

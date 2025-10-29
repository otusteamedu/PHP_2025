<?php

declare(strict_types=1);

namespace App\News\Application\UseCase\MakeNews;

use App\News\Application\GateWay\NewsParserInterface;
use App\News\Application\GateWay\NewsParserRequest;
use App\News\Domain\Factory\NewsFactoryInterface;
use App\News\Domain\Repository\NewsRepositoryInterface;

readonly class MakeNewsUseCase
{
    public function __construct(
        private NewsRepositoryInterface $repository,
        private NewsFactoryInterface $newsFactory,
        private NewsParserInterface $newsParser,
    ) {
    }

    public function __invoke(MakeNewsRequest $request): MakeNewsResponse
    {
        $response = $this->newsParser->getTitle(new NewsParserRequest($request->link));
        $news = $this->newsFactory->create($response->title, $request->link);
        $this->repository->save($news);

        return new MakeNewsResponse($news->getId()->toString());
    }
}

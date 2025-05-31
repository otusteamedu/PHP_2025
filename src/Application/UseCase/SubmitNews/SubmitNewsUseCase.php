<?php declare(strict_types=1);

namespace App\Application\UseCase\SubmitNews;

use App\Application\Service\NewsMetadataProvider\NewsMetadataProviderInterface;
use App\Domain\Factory\NewsFactoryInterface;
use App\Domain\Repository\NewsRepositoryInterface;
use App\Domain\ValueObject\Title;
use App\Domain\ValueObject\Url;

class SubmitNewsUseCase
{
    public function __construct(
        private readonly NewsFactoryInterface          $newsFactory,
        private readonly NewsRepositoryInterface       $newsRepository,
        private readonly NewsMetadataProviderInterface $newsMetadataProvider,
    )
    {
    }

    public function __invoke(SubmitNewsRequest $request): SubmitNewsResponse
    {
        $url = new Url($request->url);
        $title = new Title($this->newsMetadataProvider->fetchTitle($url->getValue()));
        $news = $this->newsFactory->create($request->url, $title->getValue());
        $this->newsRepository->save($news);

        return new SubmitNewsResponse(
            $news->getId(),
            $news->getUrl()->getValue(),
            $news->getTitle()->getValue()
        );
    }
}

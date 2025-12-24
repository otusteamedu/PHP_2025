<?php declare(strict_types=1);

namespace App\Application\UseCase\SubmitNews;

use App\Application\Service\NewsMetadataProvider\NewsMetadataProviderInterface;
use App\Application\Service\NewsMetadataProvider\NewsMetadataProviderRequest;
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

        $metaRequest = new NewsMetadataProviderRequest($url->getValue());
        $metaResponse = $this->newsMetadataProvider->fetchTitle($metaRequest);

        $title = new Title($metaResponse->title);
        $createdAt = new \DateTimeImmutable();

        $news = $this->newsFactory->create($request->url, $title->getValue(), $createdAt);
        $this->newsRepository->save($news);

        return new SubmitNewsResponse(
            $news->getId(),
            $news->getUrl()->getValue(),
            $news->getTitle()->getValue(),
            $news->getCreatedAt()->format('Y-m-d H:i:s')
        );
    }
}

<?php

namespace App\Infrastructure\UseCase;

use App\Application\DTO\News\CreateNewsDTO;
use App\Application\DTO\News\NewsDTO;
use App\Application\DTO\News\ResponseNewsDTO;
use App\Application\Factories\NewsFactory;
use App\Application\Port\NewsServiceInterface;
use App\Domain\Repository\NewsRepositoryInterface;

class CreateNewsUseCase
{
    public string $url;

    public function __construct(
        private NewsFactory  $newsFactory,
        private NewsRepositoryInterface $newsRepository,
        private NewsServiceInterface $newsService,
    ){}

    public function execute(CreateNewsDTO $createNewsDTO):ResponseNewsDTO
    {
        $url = $createNewsDTO->url;
        $arNewsTitles = $this->newsService->getHtmlByUrl($url, 'title');
        if (is_array($arNewsTitles) && !empty($arNewsTitles)) {
            $mainTitle = reset($arNewsTitles);

            $createDate = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Moscow'));
            $newsDTO = new NewsDTO($mainTitle, $url, $createDate);
            $newsEntity = $this->newsFactory->toEntity($newsDTO);

            $this->newsRepository->save($newsEntity);

            return new ResponseNewsDTO(
                $newsEntity->getId(),
                $newsEntity->getTitle(),
                $newsEntity->getUrl(),
                $createDate
            );
        } else {
            throw new \RuntimeException('This new hasn`t a title');
        }
    }
}
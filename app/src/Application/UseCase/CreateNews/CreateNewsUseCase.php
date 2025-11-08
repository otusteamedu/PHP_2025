<?php

declare(strict_types=1);

namespace App\Application\UseCase\CreateNews;

use App\Application\EventDispatcher\EventDispatcherInterface;
use App\Domain\Builder\NewsBuilder;
use App\Domain\Entity\Category;
use App\Domain\Event\NewsIsCreatedEvent;
use App\Domain\Repository\CategoryRepositoryInterface;
use App\Domain\Repository\NewsRepositoryInterface;
use App\Domain\ValueObject\CategoryName;

readonly class CreateNewsUseCase
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private NewsRepositoryInterface $newsRepository,
        private EventDispatcherInterface $eventDispatcher,
    )
    {
    }

    public function __invoke(CreateNewsRequest $request): CreateNewsResponse
    {
        $category = $this->categoryRepository->findOneByName($request->category);
        if ($category === null) {
            $category = new Category(new CategoryName($request->category));
            $this->categoryRepository->save($category);
        }

        $news = (new NewsBuilder())
            ->setTitle($request->title)
            ->setAuthor($request->author)
            ->setCategory($category)
            ->setContent($request->content)
            ->build();

        $this->newsRepository->save($news);

        $event = new NewsIsCreatedEvent(
            $news->getId(),
            $news->getTitle()->getValue(),
            $news->getAuthor()->getValue(),
            $news->getCategory()->getId(),
            $news->getContent()->getValue(),
            $news->getCreatedAt(),
        );
        $this->eventDispatcher->dispatch($event);

        return new CreateNewsResponse($news->getId());
    }
}

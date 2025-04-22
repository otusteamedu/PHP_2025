<?php

namespace App\Application\Handler;

use App\Application\Assembler\NewsAssembler;
use App\Application\Command\CreateNewsCommand;
use App\Domain\Entity\News;
use App\Domain\Repository\NewsRepository;
use App\Domain\ValueObject\News\Title;
use App\Domain\ValueObject\News\Url;
use App\Domain\ValueObject\News\CreateDate;


class CreateNewsHandler
{
    protected NewsRepository $newsRepository;
    protected NewsAssembler $newsAssembler;

    public function __construct(NewsRepository $newsRepository, NewsAssembler $newsAssembler)
    {
        $this->newsRepository = $newsRepository;
        $this->newsAssembler = $newsAssembler;
    }

    public function handle(CreateNewsCommand $command): void
    {
        $news = new News(
            new Title($command->title),
            new Url($command->url),
            new CreateDate($command->createDate),
        );

        $this->newsRepository->save($news);
    }
}
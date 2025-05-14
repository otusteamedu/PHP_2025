<?php

namespace App\Application\Factories;

use App\Application\DTO\News\NewsDTO;
use App\Domain\Entity\News;
use App\Domain\ValueObject\News\CreateDate;
use App\Domain\ValueObject\News\Title;
use App\Domain\ValueObject\News\Url;

class NewsFactory
{
    public function toEntity(NewsDTO $dto): News
    {
        $title = new Title($dto->title);
        $title = $title->getTitle();

        $url = new Url($dto->url);
        $url = $url->getUrl();

        $createDate = new CreateDate($dto->createDate);
        $createDate = $createDate->getDate();

        $news = new News();
        $news->setTitle($title)
            ->setUrl($url)
            ->setCreateDate($createDate);
        return $news;
    }
}
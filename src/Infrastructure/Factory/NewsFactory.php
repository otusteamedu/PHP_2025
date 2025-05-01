<?php

namespace App\Infrastructure\Factory;

use App\Domain\Entity\News;
use App\Domain\Factory\NewsFactoryInterface;
use App\Domain\ValueObject\Title;
use App\Domain\ValueObject\Url;

class NewsFactory implements NewsFactoryInterface
{

    public function create(string $url, string $title): News
    {
        return new News(
            new Url($url),
            new \DateTime(),
            new Title($title)
        );
    }

    public function createFromDb(array $oneNews): News
    {
        $news = new News(
            new Url($oneNews['url']),
            new \DateTime($oneNews['date']),
            new Title($oneNews['title'])
        );
        $news->setIdFromDb($oneNews['rowid']);
        return $news;
    }
}
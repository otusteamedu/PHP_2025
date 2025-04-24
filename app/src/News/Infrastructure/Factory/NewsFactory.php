<?php

declare(strict_types=1);

namespace App\News\Infrastructure\Factory;

use App\News\Domain\Entity\News;
use App\News\Domain\Entity\ValueObject\NewsLink;
use App\News\Domain\Entity\ValueObject\NewsTitle;
use App\News\Domain\Factory\NewsFactoryInterface;

class NewsFactory implements NewsFactoryInterface
{
    public function create(string $title, string $link): News
    {
        return new News(
            new NewsTitle($title),
            new NewsLink($link)
        );
    }
}

<?php

declare(strict_types=1);

namespace App\News\Infrastructure\Factory;

use App\News\Domain\Entity\News;
use App\News\Domain\Entity\ValueObject\NewsLink;
use App\News\Domain\Entity\ValueObject\NewsTitle;
use App\News\Domain\Factory\NewsFactoryInterface;
use Symfony\Component\Uid\Uuid;

class NewsFactory implements NewsFactoryInterface
{
    public function create(string $title, string $link): News
    {
        return new News(
            Uuid::v4(),
            new NewsTitle($title),
            new NewsLink($link)
        );
    }
}

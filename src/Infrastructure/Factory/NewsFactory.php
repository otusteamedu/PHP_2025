<?php declare(strict_types=1);

namespace App\Infrastructure\Factory;

use App\Domain\Entity\News;
use App\Domain\Factory\NewsFactoryInterface;
use App\Domain\ValueObject\Title;
use App\Domain\ValueObject\Url;

class NewsFactory implements NewsFactoryInterface
{
    public function create(string $url, string $title, \DateTimeImmutable $createdAt): News
    {
        return new News(
            new Url($url),
            new Title($title),
            $createdAt
        );
    }
}

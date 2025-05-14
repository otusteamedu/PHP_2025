<?php declare(strict_types=1);

namespace App\Application\DTO\News;

final class NewsDTO
{
    public readonly string $title;
    public readonly string $url;
    public readonly \DateTimeImmutable $createDate;

    public function __construct(string $title, string $url, \DateTimeImmutable $createDate)
    {
        $this->title = $title;
        $this->url = $url;
        $this->createDate = $createDate;
    }
}
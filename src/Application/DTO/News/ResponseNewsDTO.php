<?php declare(strict_types=1);

namespace App\Application\DTO\News;

final class ResponseNewsDTO
{
    public readonly int $id;
    public readonly string $title;
    public readonly string $url;
    public readonly \DateTimeImmutable|false $date;

    public function __construct(int $id, string $title, string $url, \DateTimeImmutable|false $date = false)
    {
        $this->id = $id;
        $this->title = $title;
        $this->url = $url;
        $this->date = $date;
    }
}
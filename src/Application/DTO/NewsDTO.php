<?php declare(strict_types=1);

namespace App\Application\DTO;

final class NewsDTO
{

    //TODO взять его за основу 


    public readonly string $title;
    public readonly string $url;
    public readonly \DateTimeInterface $createDate;

    public function __construct(string $title, string $url, \DateTimeInterface $createDate)
    {
        $this->title = $title;
        $this->url = $url;
        $this->createDate = $createDate;
    }
}
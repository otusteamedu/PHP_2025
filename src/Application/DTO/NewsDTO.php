<?php declare(strict_types=1);

namespace App\Application\DTO;

class NewsDTO
{
    public int $id;
    public string $title;
    public string $url;
    public \DateTimeInterface $createDate;
}
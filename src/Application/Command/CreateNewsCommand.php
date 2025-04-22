<?php

namespace App\Application\Command;

class CreateNewsCommand
{
    public string $title;
    public string $url;
    public \DateTimeInterface $createDate;

    public function __construct(string $title, string $url, \DateTimeInterface $createDate)
    {
        $this->title = $title;
        $this->url = $url;
        $this->createDate = $createDate;
    }
}
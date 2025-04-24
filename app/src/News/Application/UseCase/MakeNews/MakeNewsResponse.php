<?php

declare(strict_types=1);

namespace App\News\Application\UseCase\MakeNews;

class MakeNewsResponse
{
    public function __construct(public string $news_id)
    {
    }

}

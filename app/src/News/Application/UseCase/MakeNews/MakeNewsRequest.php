<?php

declare(strict_types=1);

namespace App\News\Application\UseCase\MakeNews;

class MakeNewsRequest
{
    public function __construct(public string $link)
    {
    }

}

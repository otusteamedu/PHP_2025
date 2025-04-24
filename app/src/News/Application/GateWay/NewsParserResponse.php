<?php

declare(strict_types=1);

namespace App\News\Application\GateWay;

class NewsParserResponse
{
    public function __construct(public string $title)
    {
    }

}

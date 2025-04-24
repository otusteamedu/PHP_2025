<?php

declare(strict_types=1);

namespace App\News\Application\GateWay;

class NewsParserRequest
{
    public function __construct(public string $url)
    {
    }

}

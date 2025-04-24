<?php

declare(strict_types=1);

namespace App\News\Application\GateWay;

interface NewsParserInterface
{
    public function getTitle(string $url): string;
}

<?php

declare(strict_types=1);

namespace App\Domain\ValueObject\News;

final class Url
{
    public function __construct(
        private string $url,
    )
    {
        $this->assertUrlIsValid($url);
    }

    public function getUrl():string
    {
        return "$this->url";
    }

    private function assertUrlIsValid(string $value):void
    {
        if (!$value) {
            throw new \InvalidArgumentException('Url is invalid');
        }
    }
}

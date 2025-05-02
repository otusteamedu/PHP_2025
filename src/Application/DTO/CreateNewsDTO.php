<?php declare(strict_types=1);

namespace App\Application\DTO;

final class CreateNewsDTO
{
    public readonly string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }
}
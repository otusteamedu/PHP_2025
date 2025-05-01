<?php

declare(strict_types=1);

namespace App\Domain\ValueObject\News;

final class Title
{
    public function __construct(
        private string $title,
    )
    {
        $this->assertTitleIsValid($title);
    }

    public function getTitle():string
    {
        return $this->title;
    }

    private function assertTitleIsValid(string $value):void
    {
        if (!preg_match('/^[a-zA-Z,\/а-яА-Я0-9\s]/', $value)) {
            throw new \InvalidArgumentException('Title is invalid');
        }
    }
}

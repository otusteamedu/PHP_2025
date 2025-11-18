<?php

declare(strict_types=1);

namespace App;

use Ubunvova\WordCount\WordCounterInterface;

final readonly class CheckLibrary
{
    public function __construct(
        private WordCounterInterface $wordCounter
    ) {
    }

    public function check(): void
    {
        $countWords = $this->wordCounter->getWordCount('Тестовый мини текст который выдаст количество слов');
        echo($countWords);
    }
}

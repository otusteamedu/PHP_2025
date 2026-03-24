<?php

declare(strict_types=1);

namespace App\FactoryMethod;

use App\FactoryMethod\DocumentCreator;
use App\FactoryMethod\IDocument;
use App\FactoryMethod\WordDocument;

final class WordDocumentCreator extends DocumentCreator
{
    protected function createDocument(): IDocument
    {
        return new WordDocument();
    }
}

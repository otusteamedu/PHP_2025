<?php

declare(strict_types=1);

namespace App\FactoryMethod;

use App\FactoryMethod\DocumentCreator;
use App\FactoryMethod\IDocument;
use App\FactoryMethod\ExcelDocument;

final class ExcelDocumentCreator extends DocumentCreator
{
    protected function createDocument(): IDocument
    {
        return new ExcelDocument();
    }
}

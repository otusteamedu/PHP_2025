<?php

declare(strict_types=1);

namespace App\FactoryMethod;

abstract class DocumentCreator
{
    abstract protected function createDocument(): IDocument;

    public function createAndSaveDocument(string $content, string $path): void
    {
        $document = $this->createDocument();
        $document->open($path);
        $document->save($content);
        $document->close();
    }
}

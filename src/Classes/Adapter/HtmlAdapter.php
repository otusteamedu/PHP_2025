<?php

namespace App\Classes\Adapter;

use App\Classes\Factory\Component\PreviewTree\PreviewTreeFile;

class HtmlAdapter implements FileAdapter
{
    private PreviewTreeFile $file;

    public function __construct(PreviewTreeFile $file)
    {
        $this->file = $file;
    }

    public function getFilePreview(): string
    {
        $filePath = $this->file->getFilePath();
        if (!file_exists($filePath)) {
            throw new \RuntimeException('File not found');
        }
        $content = file_get_contents($filePath);
        $content = strip_tags($content);

        return substr($content, 0, 50);
    }
}
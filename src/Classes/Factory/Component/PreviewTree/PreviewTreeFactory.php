<?php

namespace App\Classes\Factory\Component\PreviewTree;

use App\Classes\Factory\Component\Abstract\AbstractFile;
use App\Classes\Factory\Component\Abstract\AbstractFolder;

class PreviewTreeFactory
{
    public function createTreeFolder(string $name, string $fullPath): AbstractFolder
    {
        return new PreviewTreeFolder($name, $fullPath);
    }

    public function createTreeFile(string $name, string $fullPath): AbstractFile
    {
        return new PreviewTreeFile($name, $fullPath);
    }
}
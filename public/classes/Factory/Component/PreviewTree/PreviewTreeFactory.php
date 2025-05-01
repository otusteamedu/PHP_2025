<?php

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
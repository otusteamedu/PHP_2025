<?php

class BaseTreeFactory
{
    public function createTreeFolder(string $name): AbstractFolder
    {
        return new BaseTreeFolder($name);
    }

    public function createTreeFile(string $name): AbstractFile
    {
        return new BaseTreeFile($name);
    }
}
<?php

namespace App\Classes\Factory\Component\BaseTree;

use App\Classes\Factory\Component\Abstract\AbstractFile;
use App\Classes\Factory\Component\Abstract\AbstractFolder;

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
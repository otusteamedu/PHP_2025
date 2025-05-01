<?php

namespace App\Classes\Factory\Component\BaseTree;

use App\Classes\Factory\Component\Abstract\AbstractFile;
use App\Classes\Factory\Component\FileComponent;

class BaseTreeFile implements FileComponent, AbstractFile
{
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function display($indent = 0)
    {
        $spaces = str_repeat('-', ($indent * 4));
        echo $spaces . "- " . $this->name . PHP_EOL;
    }

    public function getFileExtension(): string
    {
        $arFile = explode(".", $this->name);
        return array_pop($arFile);
    }
}

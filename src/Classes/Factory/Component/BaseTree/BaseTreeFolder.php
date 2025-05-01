<?php

namespace App\Classes\Factory\Component\BaseTree;

use App\Classes\Factory\Component\Abstract\AbstractFolder;
use App\Classes\Factory\Component\FileComponent;

class BaseTreeFolder implements FileComponent, AbstractFolder
{
    private $name;
    private $children = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function add(FileComponent $component)
    {
        $this->children[] = $component;
    }

    public function display($indent = 0)
    {
        $spaces = str_repeat('-', ($indent * 4));
        echo $spaces . strtoupper($this->name);
        echo PHP_EOL;
        foreach ($this->children as $child) {
            $child->display($indent + 2);
        }
    }
}
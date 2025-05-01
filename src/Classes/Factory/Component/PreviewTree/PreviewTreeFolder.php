<?php

namespace App\Classes\Factory\Component\PreviewTree;

use App\Classes\Adapter\HtmlAdapter;
use App\Classes\Adapter\TxtAdapter;
use App\Classes\Factory\Component\Abstract\AbstractFolder;
use App\Classes\Factory\Component\FileComponent;

class PreviewTreeFolder implements FileComponent, AbstractFolder
{
    private $name;
    private $fullPath;
    private $children = [];

    public function __construct(string $name, string $fullPath)
    {
        $this->name = $name;
        $this->fullPath = $fullPath;
    }

    public function add(FileComponent $component)
    {
        $this->children[] = $component;
    }

    public function getSize(): int
    {
        $path = rtrim($this->fullPath, '/');
        $size = 0;
        $dir = opendir($path);
        if (!$dir) {
            return 0;
        }

        while (false !== ($file = readdir($dir))) {
            if ($file == '.' || $file == '..') {
                continue;
            } elseif (is_dir($path . $file)) {
                $size += dir_size($path . DIRECTORY_SEPARATOR . $file);
            } else {
                $size += filesize($path . DIRECTORY_SEPARATOR . $file);
            }
        }
        closedir($dir);
        return $size;
    }

    public function display($indent = 0)
    {
        $spaces = str_repeat('-', ($indent * 4));

        echo $spaces . strtoupper($this->name);
        $directorySizeInBytes = $this->getSize();
        echo ' (' . $directorySizeInBytes . ' bytes)' . PHP_EOL;


        foreach ($this->children as $child) {
            $sizeInBytes = $child->getSize();
            $child->display($indent + 2);
            if ($child instanceof PreviewTreeFile) {
                $fileExtension = $child->getFileExtension();
                if ($fileExtension == 'txt') {
                    $fileAdapter = new TxtAdapter($child);
                } elseif ($fileExtension == 'html') {
                    $fileAdapter = new HtmlAdapter($child);
                }

                $preview = $fileAdapter->getFilePreview();

                echo $spaces . $spaces . ' (' . $sizeInBytes . ' bytes)' . PHP_EOL;
                echo $spaces . $spaces . $preview . ' ' . $sizeInBytes . ' bytes';
                echo PHP_EOL;
            }
        }
    }
}
<?php

namespace App\Classes;

use App\Classes\Factory\Component\BaseTree\BaseTreeFactory;
use App\Classes\Factory\Component\PreviewTree\PreviewTreeFactory;

class DirectoryIteratorService
{
    public const DOCUMENT_ROOT = '/var/www/app/public';

    public function buildTree($path, string $mode) {
        $directoryName = basename($path);

        if ($mode == 'base') {
            $treeFactory = new BaseTreeFactory();
            $directory = $treeFactory->createTreeFolder($directoryName);
        } else if ($mode == 'preview') {
            $treeFactory = new PreviewTreeFactory();
            $directory = $treeFactory->createTreeFolder($directoryName, $path);
        }

        foreach (scandir($path) as $item) {
            if ($item === '.' || $item === '..') continue;

            $fullPath = $path . DIRECTORY_SEPARATOR . $item;
            if (is_dir($fullPath)) {
                $directory->add($this->buildTree($fullPath, $mode));
            } else {
                //TODO сюда подключить адаптер и создавать экземляр нужного типа файла
                //$directory->add(new File($item, $fullPath));
                $file = $treeFactory->createTreeFile($item, $fullPath);
                $directory->add($file);
            }
        }

        return $directory;
    }
}
<?php

namespace App\Classes\Commands;

use App\Classes\AppModesEnum;
use App\Classes\CommandInterface;
use App\Classes\DirectoryIteratorService;

class ShowFolderTree implements CommandInterface
{
    private DirectoryIteratorService $directoryIteratorService;

    function __construct(){
        $this->directoryIteratorService = new DirectoryIteratorService();
    }

    private static string $name = 'show-folder';

    public function execute(array $argv = [])
    {
        if (!isset($argv[0])) {
            throw new \RuntimeException('Error: path didn`t provided'.PHP_EOL);
        }
        $path = $argv[0];
        $mode = (isset($argv[1])) ? $argv[1] : AppModesEnum::BASE->value;

        if (!in_array($mode, AppModesEnum::getCasesValues())) {
            throw new \RuntimeException('Error: wrong mode'.PHP_EOL);
        }

        $fullPath = $this->directoryIteratorService::DOCUMENT_ROOT.$path;
        $tree = $this->directoryIteratorService->buildTree($fullPath, $mode);
        $tree->display();
    }

    public static function getName():string
    {
        return static::$name;
    }
}
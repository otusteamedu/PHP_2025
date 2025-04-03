<?php

namespace Root\App\Classes\Commands;

use Root\App\Classes\CommandInterface;
use Root\App\Classes\BookStoreService;

class FillStore implements CommandInterface
{
    protected BookStoreService $bookStoreService;

    function __construct(){
        $this->bookStoreService = new BookStoreService();
    }

    private static string $name = 'es:fill-store';

    public function execute(array $argv = [])
    {
        $this->bookStoreService->fillTheStore();
    }

    public static function getName():string
    {
        return static::$name;
    }

}
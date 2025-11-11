<?php

namespace App\Application;

interface StorageEventInterface
{
    public static function createStorage(string $storageName);
}

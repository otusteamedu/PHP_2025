<?php

namespace App\Classes\Factory;

use App\Classes\Factory\Component\Abstract\AbstractFile;
use App\Classes\Factory\Component\Abstract\AbstractFolder;

interface  FileAbstractFactory
{
    public function createFolder(): AbstractFolder;

    public function createFile(): AbstractFile;
}
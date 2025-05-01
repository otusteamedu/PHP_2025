<?php

namespace App\Classes\Factory\Component\Abstract;
interface  AbstractFile
{
    public function display($indent = 0);

    public function getFileExtension(): string;
}
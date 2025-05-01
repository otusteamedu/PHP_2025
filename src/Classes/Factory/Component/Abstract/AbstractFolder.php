<?php

namespace App\Classes\Factory\Component\Abstract;

use App\Classes\Factory\Component\FileComponent;

interface AbstractFolder
{
    public function add(FileComponent $component);

    public function display($indent = 0);
}
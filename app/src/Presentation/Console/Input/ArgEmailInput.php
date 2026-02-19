<?php

namespace Pryaniki\App\Presentation\Console\Input;

class ArgEmailInput
{
    public static function getEmail(): string
    {
        $argv = $_SERVER['argv'];

        return $argv[1] ?? '';
    }
}
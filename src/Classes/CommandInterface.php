<?php

namespace Root\App\Classes;

interface CommandInterface
{
    public function execute(array $argv = []);
    public static function getName():string;
}
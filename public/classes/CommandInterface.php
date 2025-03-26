<?php

namespace classes;

interface CommandInterface
{
    public function execute(array $argv = []);
    public static function getName():string;
}
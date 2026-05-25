<?php

namespace App\Core\Container\Config;

interface ContainerConfigLoaderInterface
{
    public function load(): array;
}

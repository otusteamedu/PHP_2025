<?php

namespace Blarkinov\Hw1500\Infrastructure\Route;

#[\Attribute(\Attribute::TARGET_METHOD)]
 class AsRoute
{
    public function __construct(
        public string $path
    ) {
    }
}
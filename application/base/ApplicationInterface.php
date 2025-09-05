<?php

namespace App\Base;

interface ApplicationInterface
{
    public function __construct();

    public function registerRoutes(): array;

    public function registerServices(): array;
}
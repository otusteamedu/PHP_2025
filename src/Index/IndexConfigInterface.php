<?php

namespace App\Index;

interface IndexConfigInterface
{
    public function getName(): string;
    
    public function getParams(): array;
}
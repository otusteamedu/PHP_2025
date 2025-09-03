<?php

namespace App\Views;

abstract class Template {
    protected $data = [];

    public function __construct(array $data = []) 
    {
        $this->data = $data;
    }

    abstract public function render(): string;
}
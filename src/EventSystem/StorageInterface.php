<?php

namespace App\EventSystem;

interface StorageInterface
{
    public function set(string $key, $value);
    public function get(string $key);
    public function del(string $key);
    public function keys(string $pattern);
}

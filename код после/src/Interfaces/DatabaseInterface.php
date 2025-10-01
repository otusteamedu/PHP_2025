<?php

namespace App\Interfaces;

interface DatabaseInterface {
    public function prepare(string $query);
}
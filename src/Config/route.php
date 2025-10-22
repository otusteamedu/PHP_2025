<?php

use Blarkinov\PhpDbCourse\Bin\Route;

return [
    new Route('/users', 'user', 'index','GET'),
    new Route('/users', 'user', 'store','POST'),
    new Route('/users/:id', 'user', 'show','GET'),
    new Route('/users/:id', 'user', 'update','PUT'),
    new Route('/users/:id', 'user', 'destroy','DELETE'),
];

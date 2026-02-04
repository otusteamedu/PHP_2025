<?php

use Illuminate\Support\Facades\Route;
use Core\Controllers\BracketController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/', [BracketController::class, 'handle']);

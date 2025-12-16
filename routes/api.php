<?php declare(strict_types=1);

use App\Http\Controllers\Api\RequestController;
use Illuminate\Support\Facades\Route;

Route::post('/v1/request', [RequestController::class, 'submitRequest']);
Route::get('/v1/request/{requestId}', [RequestController::class, 'checkStatus']);

<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/whoami', [UserController::class, 'whoami']); // TODO почему если не вводить "Accept"=>"application/json" при не правильном токене авторизации то он выдает ошибку 405
});
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);







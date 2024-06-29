<?php

use App\Http\Controllers\CommentsController;
use App\Http\Controllers\LikeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PostController;



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

    Route::get('/whoami', [UserController::class, 'show']); //FIXME: почему если не вводить "Accept"=>"application/json" при не правильном токене авторизации то он выдает ошибку 405
    Route::post('/logout', [UserController::class, 'logout']);

    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);


    Route::post('/likes/{likeableType}/{id}', [LikeController::class, 'update']);

    Route::post('/comments/{postId}', [CommentsController::class, 'store']);
    Route::put('/comments/{commentId}', [CommentsController::class, 'update']);
    Route::delete('/comments/{commentId}', [CommentsController::class, 'destroy']);
});



Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/comments/{postId}', [CommentsController::class, 'index']);

Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}', [PostController::class, 'show']);











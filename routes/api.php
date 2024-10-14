<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommentController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('posts', PostController::class)->middleware('auth:sanctum');

Route::get('/posts/user/{userId}', [PostController::class, 'postsByUser']);
Route::get('/posts/category/{categoryId}', [PostController::class, 'postsByCategory']);

// Authentication-related routes (if needed, e.g., for user registration or login)
Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');


Route::post('/posts/{postId}/comments', [CommentController::class, 'store'])->middleware('auth:sanctum');
Route::put('/posts/comments/{id}', [CommentController::class, 'update'])->middleware('auth:sanctum');
Route::delete('posts/comments/{id}', [CommentController::class, 'destroy'])->middleware('auth:sanctum');
Route::get('/comments/user/{userId}', [CommentController::class, 'commentsByUser'])->middleware('auth:sanctum');
Route::get('/comments/post/{postId}', [CommentController::class, 'commentsByPost'])->middleware('auth:sanctum');

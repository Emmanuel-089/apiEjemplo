<?php
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('post/slug/{slug}', [PostController::class, 'slug']);
Route::resource('category', CategoryController::class)->except(['create','edit']);
Route::resource('post', PostController::class)->except(['create','edit']);

Route::post('post/upload/{post}',[PostController::class, 'upload']);
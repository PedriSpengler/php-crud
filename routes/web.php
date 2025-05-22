<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource("posts", PostController::class);
    Route::put('/posts/{id}/restore', [PostController::class, 'restore'])->name('posts.restore');
    Route::delete('/posts/{id}/force-delete', [PostController::class, 'forceDelete'])->name('posts.force-delete');
    Route::resource("todos", TodoController::class);
    Route::put('/posts/{post}/completed', [PostController::class, 'updateCompleted'])->name('posts.completed');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

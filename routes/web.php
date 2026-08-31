<?php

use App\Http\Controllers\Public\CategoryArchiveController;
use App\Http\Controllers\Public\CommentController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\PageController;
use App\Http\Controllers\Public\PostController;
use App\Http\Controllers\Public\TagArchiveController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/blog', [PostController::class, 'index'])->name('public.posts.index');
Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('public.posts.show');

Route::get('/pages/{page:slug}', [PageController::class, 'show'])->name('public.pages.show');

Route::get('/categories/{category:slug}', [CategoryArchiveController::class, 'show'])->name('public.categories.show');
Route::get('/tags/{tag:slug}', [TagArchiveController::class, 'show'])->name('public.tags.show');

Route::post('/posts/{post:slug}/comments', [CommentController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('public.comments.store');

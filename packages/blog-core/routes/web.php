<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use LorneQuinn\Blog\Core\Http\Controllers\PostController;

Route::get('/posts/{slug}', [PostController::class, 'show'])->name('blog.posts.show');

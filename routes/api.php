<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::middleware('guest')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
});

// No need to be authenticated to access this route
Route::get('/posts', [PostController::class, 'index']);
Route::get('/post/{id}', [PostController::class, 'show'])->name('post.show');

// Protected Routes (requires sanctum authentication)
Route::middleware('auth:sanctum')->group(function () {
    // User Information
    Route::get('/user', function (Request $request) {
        return $request->user();
    })->name('auth.user');

    // Blog Routes
    Route::prefix('posts')->group(function () {
        Route::post('/', [PostController::class, 'store'])->name('posts.store');
        Route::put('/{id}', [PostController::class, 'edit'])->name('posts.update'); // Changed to PUT for RESTful compliance
        Route::delete('/{id}', [PostController::class, 'destroy'])->name('posts.destroy');
    });

    // Comment Routes
    Route::prefix('comments')->group(function () {
        Route::post('/', [CommentController::class, 'store'])->name('comments.store');
        Route::delete('/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    });

    // Like Routes
    Route::prefix('likes')->group(function () {
        Route::post('/', [LikeController::class, 'store'])->name('likes.store');
        Route::delete('/{id}', [LikeController::class, 'destroy'])->name('likes.destroy');
    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
});
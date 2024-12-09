<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

//no need to be authenticated to access this route
Route::get('/posts', [PostController::class, 'index']);
Route::get('/post/{id}', [PostController::class, 'getPost']);



//this route group will be protected by sanctum(need token to access)
Route::middleware('auth:sanctum')->group(function () {
    //Blog api routes
    Route::post('/add/post', [PostController::class, 'addPost']);
    Route::post('/edit/post', [PostController::class, 'editPost']);
    Route::delete('/post/{id}', [PostController::class, 'deletePost']);
    //comment api routes
    Route::post('/comment', [CommentController::class, 'postComment']);
    //Like aoi routes 
    Route::post('/like', [LikeController::class, 'addLike']);
    Route::delete('/like/{id}', [LikeController::class, 'deleteLike']);

    // Logout route
    Route::post('/logout', [AuthController::class, 'logout']);
});

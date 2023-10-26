<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::post('/register', [LoginController::class, 'createUser'])->name('verify.register');
Route::post('/login', [LoginController::class, 'login'])->name('verify.login');
Route::get('/login', [LoginController::class, 'checkAuth'])->name('login');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/api/users', [UserController::class, 'getUsers'])->name('user.get');
    Route::get('/api/users/{id}', [UserController::class, 'getSpecificUser'])->name('user.specific');
    Route::put('/api/users/update/{id}', [UserController::class, 'updateUser'])->name('user.update');
    Route::delete('/api/users/delete/{id}', [UserController::class, 'deleteUser'])->name('user.delete');

    Route::get('/api/posts', [PostController::class, 'getPosts'])->name('post.get');
    Route::get('/api/posts/{id}', [PostController::class, 'getSpecificPost'])->name('post.specific');
    Route::post('/api/posts/create', [PostController::class, 'createPost'])->name('post.create');
    Route::put('/api/posts/update/{id}', [PostController::class, 'updatePost'])->name('post.update');
    Route::delete('/api/posts/delete/{id}', [PostController::class, 'deletePost'])->name('post.delete');
});
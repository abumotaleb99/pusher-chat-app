<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChatController;

Route::redirect('/', 'login');
Route::get('register', [AuthController::class, 'showRegisterForm'])->name('auth.register');
Route::post('register', [AuthController::class, 'register'])->name('auth.register.submit');
Route::get('login', [AuthController::class, 'showLoginForm'])->name('auth.login');
Route::post('login', [AuthController::class, 'login'])->name('auth.login.submit');

Route::middleware('auth')->group(function () {
    // Logout
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    
    // Profile
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // Chat
    Route::get('chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('conversations', [ChatController::class, 'getConversations']);
    Route::get('users', [ChatController::class, 'searchUsers']);
    Route::get('/messages/{user}', [ChatController::class, 'getMessages']);
    Route::post('/messages/{user}', [ChatController::class, 'sendMessage']);

    Route::get('/users/{id}', [ChatController::class, 'show']);
});
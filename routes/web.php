<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return Auth::check() ? redirect('/produtos') : redirect('auth.login');
});

Route::middleware('guest')->group( function() {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
});

Route::middleware('auth')->group( function(){
      Route::get('/produtos', [ProductController::class, 'index'])->name('produtos.index');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

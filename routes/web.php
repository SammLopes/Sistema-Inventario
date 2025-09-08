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

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/produtos', [ProductController::class, 'index'])->name('products.index');
    Route::put('/produtos/{product}/estoque', [ProductController::class, 'updateStock'])->name('products.update-stock');
    Route::post('/produtos/sync-api', [ProductController::class, 'syncWithApi'])->name('products.sync-api');

});

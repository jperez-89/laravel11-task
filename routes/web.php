<?php

use Illuminate\Support\Facades\Route;

// Route::view('/', 'welcome');

// Entrar directamente al dashboard si el usuario ya está autenticado.
Route::redirect('/', '/dashboard');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Route::view('/', 'welcome');

// Entrar directamente al dashboard si el usuario ya está autenticado.
Route::redirect('/', '/dashboard');

// Ejecuta la vista con el controlador DashboardController.
Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

require __DIR__.'/auth.php';

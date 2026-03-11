<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CardSourceColorSettingsController;
use Illuminate\Support\Facades\Route;
use App\Livewire\ApplicationsBoard;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/settings/card-source-colors', [CardSourceColorSettingsController::class, 'edit'])
        ->name('settings.card-source-colors.edit');
    Route::patch('/settings/card-source-colors', [CardSourceColorSettingsController::class, 'update'])
        ->name('settings.card-source-colors.update');
    
    Route::get('/dashboard', ApplicationsBoard::class)
        ->name('dashboard');
});

require __DIR__ . '/auth.php';

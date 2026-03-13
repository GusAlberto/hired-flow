<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;
use App\Livewire\ApplicationsBoard;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/demo', function () {
    return view('demo');
})->name('demo');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings/archiving', [SettingsController::class, 'updateArchiving'])->name('settings.archiving.update');
    Route::post('/settings/archiving/run-now', [SettingsController::class, 'runArchivingNow'])->name('settings.archiving.run-now');
    
    Route::get('/dashboard', ApplicationsBoard::class)
        ->middleware('verified.when-enabled')
        ->name('dashboard');
});

require __DIR__ . '/auth.php';

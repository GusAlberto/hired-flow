<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ApplicationPageController;
use Illuminate\Support\Facades\Route;
use App\Livewire\DashboardPage;
use App\Livewire\BoardPage;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/demo', function () {
    return view('demo');
})->name('demo');

Route::view('/terms', 'legal.terms')->name('terms');
Route::view('/privacy', 'legal.privacy')->name('privacy');
Route::view('/contact', 'legal.contact')->name('contact');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::patch('/settings/archiving', [SettingsController::class, 'updateArchiving'])->name('settings.archiving.update');
    Route::post('/settings/archiving/run-now', [SettingsController::class, 'runArchivingNow'])->name('settings.archiving.run-now');
    
    Route::get('/dashboard', DashboardPage::class)
        ->middleware('verified.when-enabled')
        ->name('dashboard');

    Route::get('/board', BoardPage::class)
        ->middleware('verified.when-enabled')
        ->name('board');

    Route::get('/applications/create', [ApplicationPageController::class, 'create'])
        ->middleware('verified.when-enabled')
        ->name('applications.create');

    Route::post('/applications', [ApplicationPageController::class, 'store'])
        ->middleware('verified.when-enabled')
        ->name('applications.store');
});

require __DIR__ . '/auth.php';

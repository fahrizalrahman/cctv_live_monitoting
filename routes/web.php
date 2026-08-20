<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\Admin\CctvController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\CctvGroupController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MapController::class, 'index'])->name('map');

// API endpoint for real-time status checking
Route::get('/api/cctvs/status', [\App\Http\Controllers\Api\CctvStatusController::class, 'index'])->name('api.cctvs.status');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Group
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::post('cctvs/bulk-visibility', [CctvController::class, 'bulkVisibility'])->name('cctvs.bulk_visibility');
        Route::patch('cctvs/{cctv}/toggle-visibility', [CctvController::class, 'toggleVisibility'])->name('cctvs.toggle_visibility');
        Route::resource('cctvs', CctvController::class);
        Route::resource('groups', CctvGroupController::class);
        
        // Users
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Roles
        Route::resource('roles', RoleController::class)->except(['show']);

        // Menus
        Route::resource('menus', MenuController::class)->except(['show']);

        // Settings
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    });
});

require __DIR__.'/auth.php';

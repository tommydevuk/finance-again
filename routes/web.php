<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

use App\Http\Controllers\SystemController;

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/system', [SystemController::class, 'index'])->name('system.dashboard');
    
    Route::prefix('system')->name('system.')->group(function () {
        Route::get('/roles', [App\Http\Controllers\System\RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/{role}/permissions', [App\Http\Controllers\System\RoleController::class, 'editPermissions'])->name('roles.permissions.edit');
        Route::put('/roles/{role}/permissions', [App\Http\Controllers\System\RoleController::class, 'updatePermissions'])->name('roles.permissions.update');
    });
});

require __DIR__.'/settings.php';

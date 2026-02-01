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
use App\Http\Controllers\UserDashboardController;
use App\Http\Middleware\LoginRoleRedirectMiddleware;

Route::get('dashboard', UserDashboardController::class)
    ->middleware(['auth', 'verified', LoginRoleRedirectMiddleware::class])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('teams/{entity:uuid}', [\App\Http\Controllers\TeamController::class, 'show'])->name('teams.show');

    Route::get('/system', [SystemController::class, 'index'])->name('system.dashboard');

    Route::prefix('system')->name('system.')->group(function () {
        Route::post('users/{user}/impersonate', [\App\Http\Controllers\System\ImpersonationController::class, 'store'])->name('users.impersonate');
        Route::get('users/{user:uuid}', [\App\Http\Controllers\System\UserController::class, 'show'])->name('users.show');
        Route::resource('users', \App\Http\Controllers\System\UserController::class)->except(['show']);
        Route::resource('platforms', \App\Http\Controllers\System\PlatformController::class);
        Route::resource('currencies', \App\Http\Controllers\System\CurrencyController::class);
        Route::get('/roles', [App\Http\Controllers\System\RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/{role}/permissions', [App\Http\Controllers\System\RoleController::class, 'editPermissions'])->name('roles.permissions.edit');
        Route::put('/roles/{role}/permissions', [App\Http\Controllers\System\RoleController::class, 'updatePermissions'])->name('roles.permissions.update');
    });
});

require __DIR__.'/settings.php';

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

use App\Http\Controllers\TeamController;
use App\Http\Middleware\SetTeamContext;

Route::get('dashboard', UserDashboardController::class)
    ->middleware(['auth', 'verified', LoginRoleRedirectMiddleware::class])
    ->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('teams/create', [TeamController::class, 'create'])->name('teams.create');
    Route::post('teams', [TeamController::class, 'store'])->name('teams.store');
    Route::prefix('teams/{entity:uuid}')->middleware(SetTeamContext::class)->name('teams.')->group(function () {
        Route::get('/', [TeamController::class, 'show'])->name('show');
        
        // Roles Management
        Route::get('/roles', [\App\Http\Controllers\Team\RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/{role}/permissions', [\App\Http\Controllers\Team\RoleController::class, 'editPermissions'])->name('roles.permissions.edit');
        Route::put('/roles/{role}/permissions', [\App\Http\Controllers\Team\RoleController::class, 'updatePermissions'])->name('roles.permissions.update');

        // Users Management
        Route::get('/users', [\App\Http\Controllers\Team\UserController::class, 'index'])->name('users.index');

        // Projects Management
        Route::get('/projects', [\App\Http\Controllers\Team\ProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/create', [\App\Http\Controllers\Team\ProjectController::class, 'create'])->name('projects.create');
        Route::post('/projects', [\App\Http\Controllers\Team\ProjectController::class, 'store'])->name('projects.store');
        Route::get('/projects/{project:uuid}', [\App\Http\Controllers\Team\ProjectController::class, 'show'])->name('projects.show');
        Route::get('/projects/{project:uuid}/users', [\App\Http\Controllers\Team\ProjectController::class, 'editUsers'])->name('projects.users.edit');
        Route::put('/projects/{project:uuid}/users', [\App\Http\Controllers\Team\ProjectController::class, 'updateUsers'])->name('projects.users.update');

        // Task Management
        Route::post('/projects/{project:uuid}/tasks', [\App\Http\Controllers\Team\TaskController::class, 'store'])->name('projects.tasks.store');
        Route::put('/projects/{project:uuid}/tasks/{task:uuid}', [\App\Http\Controllers\Team\TaskController::class, 'update'])->name('projects.tasks.update');
        Route::delete('/projects/{project:uuid}/tasks/{task:uuid}', [\App\Http\Controllers\Team\TaskController::class, 'destroy'])->name('projects.tasks.destroy');
        Route::post('/projects/{project:uuid}/tasks/reorder', [\App\Http\Controllers\Team\TaskController::class, 'reorder'])->name('projects.tasks.reorder');
    });

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

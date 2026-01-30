<?php

namespace App\Providers;

use App\Enums\RolesEnum;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            // Check for Super Admin role within an entity of type 'system'
            // We use a direct DB check to avoid any Spatie 'current team' scoping issues
            return \Illuminate\Support\Facades\DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->join('entities', 'model_has_roles.entity_id', '=', 'entities.id')
                ->where('roles.name', RolesEnum::SUPER_ADMIN->value)
                ->where('entities.type', 'system')
                ->where('model_has_roles.model_id', $user->id)
                ->where('model_has_roles.model_type', get_class($user))
                ->exists() ? true : null;
        });

        Gate::define('viewSystemDashboard', function ($user) {
            return \Illuminate\Support\Facades\DB::table('model_has_roles')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->join('entities', 'model_has_roles.entity_id', '=', 'entities.id')
                ->where('roles.name', RolesEnum::SUPER_ADMIN->value)
                ->where('entities.type', 'system')
                ->where('model_has_roles.model_id', $user->id)
                ->where('model_has_roles.model_type', get_class($user))
                ->exists();
        });

        $this->configureDefaults();
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null
        );
    }
}

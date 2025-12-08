<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Equipo;
use App\Observers\EquipoObserver;

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
        Equipo::observe(EquipoObserver::class);
    }
}

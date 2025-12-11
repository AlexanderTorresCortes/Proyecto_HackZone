<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use App\Models\Equipo;
use App\Models\EquipoMiembro;
use App\Models\Evaluacion;
use App\Observers\EquipoObserver;
use App\Observers\EquipoMiembroObserver;
use App\Observers\EvaluacionObserver;

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
        // Observer para cuando se crea un equipo
        Equipo::observe(EquipoObserver::class);

        // Observer para cuando un usuario se une/sale de un equipo
        EquipoMiembro::observe(EquipoMiembroObserver::class);

        // Observer para cuando un juez califica un proyecto
        Evaluacion::observe(EvaluacionObserver::class);
    }
}

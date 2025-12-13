<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Models\Equipo;
use App\Models\EquipoMiembro;
use App\Models\Evaluacion;
use App\Observers\EquipoObserver;
use App\Observers\EquipoMiembroObserver;
use App\Observers\EvaluacionObserver;
use Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;

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
        // Registrar transport de Brevo para envío de correos
        Mail::extend('brevo', function (array $config) {
            if (!isset($config['key']) || empty($config['key'])) {
                throw new \RuntimeException('Brevo API key is not configured');
            }

            $factory = new BrevoTransportFactory();
            $dsn = Dsn::fromString('brevo+api://' . $config['key'] . '@default');

            return $factory->create($dsn);
        });

        // Observer para cuando se crea un equipo
        Equipo::observe(EquipoObserver::class);

        // Observer para cuando un usuario se une/sale de un equipo
        EquipoMiembro::observe(EquipoMiembroObserver::class);

        // Observer para cuando un juez califica un proyecto
        Evaluacion::observe(EvaluacionObserver::class);

        if($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}

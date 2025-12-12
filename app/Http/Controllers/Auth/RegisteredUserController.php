<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            event(new Registered($user));

            // Enviar correo de bienvenida con Brevo (de forma síncrona)
            try {
                $mailEnabled = env('MAIL_ENABLED', 'false');
                if ($mailEnabled === 'true' || $mailEnabled === true) {
                    \Log::info('Enviando correo de bienvenida a: ' . $user->email);
                    Mail::to($user->email)->send(new WelcomeEmail($user));
                    \Log::info('Correo de bienvenida enviado exitosamente');
                } else {
                    \Log::info('Envío de correos deshabilitado (MAIL_ENABLED=false)');
                }
            } catch (\Exception $e) {
                // Si falla el correo, no detiene el registro del usuario
                \Log::error('Error al enviar correo de bienvenida: ' . $e->getMessage());
                \Log::error('El usuario se registró correctamente, pero el correo no se pudo enviar');
            }

            Auth::login($user);

            return redirect('/dashboard');
        } catch (\Exception $e) {
            \Log::error('Error al registrar usuario: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()->withErrors([
                'email' => 'Hubo un error al registrar tu cuenta. Por favor, intenta nuevamente.'
            ])->withInput($request->except('password', 'password_confirmation'));
        }
    }
}
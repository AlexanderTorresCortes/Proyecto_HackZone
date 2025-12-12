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

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        // Enviar correo de bienvenida (de forma asíncrona)
        // Si falla el correo, no detiene el registro del usuario
        try {
            // Verificar si el correo está habilitado
            if (env('MAIL_ENABLED', false)) {
                Mail::to($user->email)->send(new WelcomeEmail($user));
                session()->flash('mail_status', '¡Correo de bienvenida enviado a: ' . $user->email);
            }
        } catch (\Exception $e) {
            // Si falla el correo, no detiene el registro
            \Log::error('Error al enviar correo de bienvenida: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            // No mostrar error al usuario para no interrumpir el registro
        }

        Auth::login($user);

        return redirect('/dashboard');
    }
}
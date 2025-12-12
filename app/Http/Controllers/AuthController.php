<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;
use Illuminate\Database\QueryException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login_hackzone');
    }

    public function showRegister()
    {
        return view('registro');
    }

    public function login(Request $request)
    {
        $request->validate([
            'usuario' => 'required|string',
            'password' => 'required|string',
            'rol' => 'required|in:usuario,administrador,juez',
        ]);

        $user = User::where('username', $request->usuario)
                    ->orWhere('email', $request->usuario)
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'usuario' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
            ])->withInput($request->only('usuario'));
        }

        if ($user->rol !== $request->rol) {
            return back()->withErrors([
                'rol' => 'El rol seleccionado no coincide con tu cuenta.',
            ])->withInput($request->only('usuario'));
        }

        Auth::login($user, $request->filled('remember'));

        $request->session()->regenerate();

        return $this->redirectBasedOnRole($user);
    }

    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'usuario' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'usuario.required' => 'El usuario es obligatorio.',
            'usuario.unique' => 'Este usuario ya está registrado.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debe ser un correo electrónico válido.',
            'email.unique' => 'Este correo ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        try {
            // Crear usuario de forma más explícita para evitar problemas con casts
            $user = new User();
            $user->name = $request->nombre;
            $user->username = $request->usuario;
            $user->email = $request->email;
            $user->password = $request->password; // El cast 'hashed' lo hasheará automáticamente
            $user->rol = 'usuario';
            $user->save();

            Log::info('Usuario registrado exitosamente: ' . $user->email . ' (ID: ' . $user->id . ')');

            return redirect()->route('login.form')->with('success', 'Registrado exitosamente.');
        } catch (\Illuminate\Database\QueryException $e) {
            Log::error('Error de base de datos al registrar usuario: ' . $e->getMessage());
            Log::error('SQL: ' . $e->getSql());
            Log::error('Bindings: ' . json_encode($e->getBindings()));
            
            // Verificar si es un error de duplicado
            if ($e->getCode() == 23000) {
                return back()->withErrors([
                    'email' => 'Este correo o usuario ya está registrado.'
                ])->withInput($request->except('password', 'password_confirmation'));
            }
            
            return back()->withErrors([
                'error' => 'Error de base de datos. Por favor, verifica que las migraciones se hayan ejecutado correctamente.'
            ])->withInput($request->except('password', 'password_confirmation'));
        } catch (\Exception $e) {
            Log::error('Error al registrar usuario: ' . $e->getMessage());
            Log::error('Tipo: ' . get_class($e));
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return back()->withErrors([
                'error' => 'Hubo un error al registrar tu cuenta: ' . $e->getMessage()
            ])->withInput($request->except('password', 'password_confirmation'));
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Sesión cerrada exitosamente.');
    }

    protected function redirectBasedOnRole(User $user)
    {
        return match($user->rol) {
            'administrador' => redirect()->route('admin.dashboard')->with('success', 'Bienvenido, Administrador.'),
            'juez' => redirect()->route('juez.dashboard')->with('success', 'Bienvenido, Juez.'),
            'usuario' => redirect()->route('inicio.index')->with('success', 'Bienvenido, Usuario.'),
            default => redirect()->route('inicio.index'),
        };
    }
}
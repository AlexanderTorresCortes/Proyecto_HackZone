<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
        Log::info('=== INICIO DE REGISTRO ===');
        Log::info('Datos recibidos: ' . json_encode($request->except('password', 'password_confirmation')));
        
        try {
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
            
            Log::info('Validación pasada correctamente');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Error de validación: ' . json_encode($e->errors()));
            throw $e;
        }

        try {
            Log::info('Intentando crear usuario...');
            
            // Verificar conexión a BD
            try {
                \DB::connection()->getPdo();
                Log::info('Conexión a BD exitosa');
            } catch (\Exception $e) {
                Log::error('Error de conexión a BD: ' . $e->getMessage());
                throw new \Exception('No se pudo conectar a la base de datos: ' . $e->getMessage());
            }
            
            // Verificar que la tabla users existe
            try {
                $tableExists = \Schema::hasTable('users');
                Log::info('Tabla users existe: ' . ($tableExists ? 'Sí' : 'No'));
                if (!$tableExists) {
                    throw new \Exception('La tabla users no existe. Ejecuta: php artisan migrate');
                }
            } catch (\Exception $e) {
                Log::error('Error verificando tabla: ' . $e->getMessage());
                throw $e;
            }
            
            // Crear usuario de forma más explícita para evitar problemas con casts
            $user = new User();
            $user->name = $request->nombre;
            $user->username = $request->usuario;
            $user->email = $request->email;
            $user->password = $request->password; // El cast 'hashed' lo hasheará automáticamente
            $user->rol = 'usuario';
            
            Log::info('Datos del usuario preparados, guardando...');
            $user->save();
            
            Log::info('Usuario guardado exitosamente. ID: ' . $user->id);

            Log::info('Usuario registrado exitosamente: ' . $user->email . ' (ID: ' . $user->id . ')');

            return redirect()->route('login.form')->with('success', 'Registrado exitosamente.');
        } catch (\Illuminate\Database\QueryException $e) {
            $errorCode = $e->getCode();
            $errorMessage = $e->getMessage();
            
            Log::error('=== ERROR DE BASE DE DATOS AL REGISTRAR USUARIO ===');
            Log::error('Código: ' . $errorCode);
            Log::error('Mensaje: ' . $errorMessage);
            
            // Intentar obtener SQL si está disponible
            if (method_exists($e, 'getSql')) {
                Log::error('SQL: ' . $e->getSql());
                Log::error('Bindings: ' . json_encode($e->getBindings()));
            }
            Log::error('Stack trace: ' . $e->getTraceAsString());
            Log::error('==================================================');
            
            // Verificar si es un error de duplicado
            if ($errorCode == 23000 || str_contains($errorMessage, 'Duplicate entry')) {
                return back()->withErrors([
                    'email' => 'Este correo o usuario ya está registrado.'
                ])->withInput($request->except('password', 'password_confirmation'));
            }
            
            // Verificar si es un error de tabla no existe
            if (str_contains($errorMessage, "doesn't exist") || str_contains($errorMessage, 'Table') && str_contains($errorMessage, 'not found')) {
                return back()->withErrors([
                    'error' => 'Error: La tabla de usuarios no existe. Por favor, ejecuta las migraciones: php artisan migrate'
                ])->withInput($request->except('password', 'password_confirmation'));
            }
            
            return back()->withErrors([
                'error' => 'Error de base de datos: ' . $errorMessage
            ])->withInput($request->except('password', 'password_confirmation'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Si es un error de validación, dejarlo pasar para que Laravel lo maneje
            throw $e;
        } catch (\Exception $e) {
            Log::error('=== ERROR GENERAL AL REGISTRAR USUARIO ===');
            Log::error('Tipo: ' . get_class($e));
            Log::error('Mensaje: ' . $e->getMessage());
            Log::error('Código: ' . $e->getCode());
            Log::error('Archivo: ' . $e->getFile());
            Log::error('Línea: ' . $e->getLine());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            Log::error('==========================================');
            
            return back()->withErrors([
                'error' => 'Error inesperado: ' . $e->getMessage() . ' (Línea: ' . $e->getLine() . ')'
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
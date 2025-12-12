<?php

use App\Models\Event;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\EventosController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EquiposController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\MensajesController;
use App\Http\Controllers\Admin\EventoAdminController;
use App\Http\Controllers\Juez\JuezDashboardController;
use App\Http\Controllers\TeamRequestController;
use App\Http\Controllers\Usuario\EntregasController;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;
use App\Models\User;


// ============================================
// RUTAS PÚBLICAS
// ============================================
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login.form');
Route::post('/login-data', [AuthController::class, 'login'])->name('login.submit');
Route::get('/registro', [AuthController::class, 'showRegister'])->name('register.form');
Route::post('/register-data', [AuthController::class, 'register'])->name('register.submit');

// Ruta de diagnóstico (temporal - eliminar después)
Route::get('/test-db', function() {
    try {
        \Log::info('=== TEST DB ===');
        \DB::connection()->getPdo();
        \Log::info('Conexión OK');
        
        $tableExists = \Schema::hasTable('users');
        \Log::info('Tabla users existe: ' . ($tableExists ? 'Sí' : 'No'));
        
        $userCount = \App\Models\User::count();
        \Log::info('Usuarios en BD: ' . $userCount);
        
        return response()->json([
            'status' => 'ok',
            'db_connected' => true,
            'users_table_exists' => $tableExists,
            'user_count' => $userCount,
            'env' => [
                'DB_CONNECTION' => env('DB_CONNECTION'),
                'DB_HOST' => env('DB_HOST'),
                'DB_DATABASE' => env('DB_DATABASE'),
                'DB_USERNAME' => env('DB_USERNAME'),
            ]
        ]);
    } catch (\Exception $e) {
        \Log::error('Error en test-db: ' . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 500);
    }
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/inicio', [InicioController::class, 'index'])->name('inicio.index');
Route::get('/eventos', [EventosController::class, 'index'])->name('eventos.index');
Route::get('/eventos/{id}', [EventosController::class, 'show'])->name('eventos.show');
Route::get('/eventos/{id}/resultados', [EventosController::class, 'verResultados'])->name('eventos.resultados');
Route::post('/eventos/{id}/inscribir', [EventosController::class, 'inscribir'])->name('eventos.inscribir')->middleware('auth');

// Rutas de Mensajes (requieren autenticación)
Route::middleware(['auth'])->group(function () {
    Route::get('/mensajes', [MensajesController::class, 'index'])->name('mensajes.index');
    Route::get('/mensajes/chat/{chatId}', [MensajesController::class, 'ver'])->name('mensajes.ver');
    Route::post('/mensajes/enviar', [MensajesController::class, 'enviar'])->name('mensajes.enviar');
    Route::post('/mensajes/iniciar', [MensajesController::class, 'iniciarChat'])->name('mensajes.iniciar');
    Route::get('/mensajes/chat/{chatId}/obtener', [MensajesController::class, 'obtenerMensajes'])->name('mensajes.obtener');
});

// ============================================
// RUTAS DE EQUIPOS (Públicas y Autenticadas)
// ============================================

// Rutas públicas de equipos (cualquiera puede ver)
Route::get('/equipos', [EquiposController::class, 'index'])->name('equipos.index');
Route::get('/equipos/{id}', [EquiposController::class, 'show'])->name('equipos.show');

// Rutas protegidas de equipos (solo usuarios normales)
Route::middleware(['auth', 'role:usuario'])->group(function () {
    // Crear equipo
    Route::get('/equipos/crear', [EquiposController::class, 'create'])->name('equipos.create');
    Route::post('/equipos', [EquiposController::class, 'store'])->name('equipos.store');

    // Solicitar unirse a un equipo
    Route::post('/equipos/{id}/solicitar', [EquiposController::class, 'solicitarUnirse'])->name('equipos.solicitarUnirse');

    // Gestión de solicitudes (solo líder)
    Route::post('/solicitudes/{id}/aceptar', [EquiposController::class, 'aceptarSolicitud'])->name('equipos.aceptarSolicitud');
    Route::post('/solicitudes/{id}/rechazar', [EquiposController::class, 'rechazarSolicitud'])->name('equipos.rechazarSolicitud');

    // Ver todas las solicitudes de mis equipos
    Route::get('/mis-solicitudes', [EquiposController::class, 'verSolicitudes'])->name('equipos.solicitudes');

    // Gestión de invitaciones (solo líder)
    Route::get('/equipos/{id}/buscar-usuarios', [EquiposController::class, 'buscarUsuarios'])->name('equipos.buscarUsuarios');
    Route::post('/equipos/{id}/enviar-invitacion', [EquiposController::class, 'enviarInvitacion'])->name('equipos.enviarInvitacion');
    
    // Aceptar/rechazar invitaciones
    Route::post('/invitaciones/{id}/aceptar', [EquiposController::class, 'aceptarInvitacion'])->name('equipos.aceptarInvitacion');
    Route::post('/invitaciones/{id}/rechazar', [EquiposController::class, 'rechazarInvitacion'])->name('equipos.rechazarInvitacion');
    
    // Asignar rol a miembro (solo líder)
    Route::post('/equipos/asignar-rol/{miembroId}', [EquiposController::class, 'asignarRol'])->name('equipos.asignarRol');

    // Cambiar acceso del equipo (solo líder)
    Route::post('/equipos/{id}/cambiar-acceso', [EquiposController::class, 'cambiarAcceso'])->name('equipos.cambiarAcceso');
});

// ============================================
// RUTAS AUTENTICADAS (Usuario)
// ============================================
Route::middleware(['auth'])->group(function () {

    // PERFIL
    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil.index');
    Route::get('/perfil/editar', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil/actualizar', [PerfilController::class, 'update'])->name('perfil.update');
    Route::get('/certificados/{id}/descargar', [PerfilController::class, 'descargarCertificado'])->name('certificados.descargar');

    // DASHBOARD USUARIO
    Route::get('/usuario/dashboard', function () {
        return view('usuario.dashboard');
    })->name('usuario.dashboard');

    // ENTREGAS DE PROYECTOS
    Route::prefix('usuario')->name('usuario.')->group(function () {
        Route::get('/entregas', [EntregasController::class, 'index'])->name('entregas.index');
        Route::post('/entregas', [EntregasController::class, 'store'])->name('entregas.store');
        Route::get('/entregas/{id}/descargar', [EntregasController::class, 'download'])->name('entregas.download');
        Route::delete('/entregas/{id}', [EntregasController::class, 'destroy'])->name('entregas.destroy');
    });
});

// ============================================
// RUTAS DE ADMINISTRADOR
// ============================================
Route::middleware(['auth', 'role:administrador'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/usuarios', [AdminDashboardController::class, 'usuarios'])->name('usuarios.index');
    Route::get('/usuarios/exportar/excel', [AdminDashboardController::class, 'exportarUsuariosExcel'])->name('usuarios.exportar.excel');
    Route::get('/usuarios/exportar/pdf', [AdminDashboardController::class, 'exportarUsuariosPDF'])->name('usuarios.exportar.pdf');
    Route::get('/usuarios/aprobar', [AdminDashboardController::class, 'aprobarUsuarios'])->name('usuarios.aprobar');
    Route::delete('/usuarios/{id}', [AdminDashboardController::class, 'destroyUsuario'])->name('usuarios.destroy');
    Route::get('/equipos', [AdminDashboardController::class, 'equipos'])->name('equipos.index');
    Route::get('/equipos/exportar/excel', [AdminDashboardController::class, 'exportarEquiposExcel'])->name('equipos.exportar.excel');
    Route::get('/equipos/exportar/pdf', [AdminDashboardController::class, 'exportarEquiposPDF'])->name('equipos.exportar.pdf');
    Route::delete('/equipos/{id}', [AdminDashboardController::class, 'destroyEquipo'])->name('equipos.destroy');
    Route::get('/calendario', function() {
        $eventos = Event::all();
        return view('admin.calendario', compact('eventos'));
    })->name('calendario');
    
    // ===== RUTAS DE EVENTOS (NUEVAS) =====
    Route::prefix('eventos')->name('eventos.')->group(function () {
        Route::get('/', [EventoAdminController::class, 'index'])->name('index');
        Route::get('/crear', [EventoAdminController::class, 'create'])->name('create');
        Route::post('/', [EventoAdminController::class, 'store'])->name('store');
        Route::get('/{id}/editar', [EventoAdminController::class, 'edit'])->name('edit');
        Route::put('/{id}', [EventoAdminController::class, 'update'])->name('update');
        Route::delete('/{id}', [EventoAdminController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/finalizar', [EventoAdminController::class, 'finalizar'])->name('finalizar');
    });
    
    // Sistema de Logros
    Route::get('/logros', [AdminDashboardController::class, 'logros'])->name('logros');

    // Gestión de Archivos
    Route::get('/archivos', [AdminDashboardController::class, 'archivos'])->name('archivos');

    // Gestión de Evaluaciones
    Route::get('/evaluaciones', [AdminDashboardController::class, 'evaluaciones'])->name('evaluaciones');

    // Ver ranking de evento
    Route::get('/eventos/{id}/ranking', [AdminDashboardController::class, 'verRanking'])->name('eventos.ranking');

    Route::get('/backup', [AdminDashboardController::class, 'backup'])->name('backup');
    Route::get('/permisos', [AdminDashboardController::class, 'permisos'])->name('permisos');
    Route::post('/permisos/asignar-juez', [AdminDashboardController::class, 'asignarJuez'])->name('permisos.asignar-juez');
    Route::post('/permisos/cambiar-rol', [AdminDashboardController::class, 'cambiarRol'])->name('permisos.cambiar-rol');
    Route::get('/reportes/generar', [AdminDashboardController::class, 'generarReporte'])->name('reportes.generar');
    Route::put('/equipos/{id}', [AdminDashboardController::class, 'updateEquipo'])->name('equipos.update');
});

// ============================================
// RUTAS DE JUEZ
// ============================================
Route::middleware(['auth', 'role:juez'])->prefix('juez')->name('juez.')->group(function () {
    // Dashboard del juez
    Route::get('/dashboard', [JuezDashboardController::class, 'index'])->name('dashboard');

    // Ver equipos de un evento
    Route::get('/eventos/{eventoId}/equipos', [JuezDashboardController::class, 'verEquipos'])->name('equipos');

    // Evaluar un equipo
    Route::get('/eventos/{eventoId}/equipos/{equipoId}/evaluar', [JuezDashboardController::class, 'evaluarEquipo'])->name('evaluar');

    // Guardar evaluación
    Route::post('/eventos/{eventoId}/equipos/{equipoId}/evaluar', [JuezDashboardController::class, 'guardarEvaluacion'])->name('guardar-evaluacion');

    // Ver ranking del evento
    Route::get('/eventos/{eventoId}/ranking', [JuezDashboardController::class, 'verRanking'])->name('ranking');
});

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    
    // Enviar solicitud para unirse a un equipo
    Route::post('/teams/{team}/request', [TeamRequestController::class, 'store'])
        ->name('teams.request.store');
    
    // Aceptar solicitud
    Route::post('/team-requests/{teamRequest}/accept', [TeamRequestController::class, 'accept'])
        ->name('team-requests.accept');
    
    // Rechazar solicitud
    Route::post('/team-requests/{teamRequest}/reject', [TeamRequestController::class, 'reject'])
        ->name('team-requests.reject');
});

Route::get('/test-email', function () {
    $user = User::first(); // Toma el primer usuario
    
    if (!$user) {
        return 'No hay usuarios en la base de datos. Regístrate primero.';
    }
    
    try {
        Mail::to($user->email)->send(new WelcomeEmail($user));
        return '¡Correo enviado exitosamente a ' . $user->email . '! Revisa tu bandeja de entrada.';
    } catch (\Exception $e) {
        return 'ERROR AL ENVIAR: ' . $e->getMessage();
    }
});
<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\EventosController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EquiposController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\Admin\AdminDashboardController;

// RUTAS PÚBLICAS
Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login.form');
Route::post('/login-data', [AuthController::class, 'login'])->name('login.submit');
Route::get('/registro', [AuthController::class, 'showRegister'])->name('register.form');
Route::post('/register-data', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/inicio', [InicioController::class, 'index'])->name('inicio.index');
Route::get('/eventos', [EventosController::class, 'index'])->name('eventos.index');
Route::get('/eventos/{id}', [EventosController::class, 'show'])->name('eventos.show');
Route::get('/equipos', [EquiposController::class, 'index'])->name('equipos.index');
Route::post('/equipos', [EquiposController::class, 'store'])->name('equipos.store');

// RUTAS AUTENTICADAS
Route::middleware(['auth'])->group(function () {
    
    // PERFIL
    Route::get('/perfil', [PerfilController::class, 'index'])->name('perfil.index');
    Route::get('/perfil/editar', [PerfilController::class, 'edit'])->name('perfil.edit');
    Route::put('/perfil/actualizar', [PerfilController::class, 'update'])->name('perfil.update');
    
    // DASHBOARD USUARIO
    Route::get('/usuario/dashboard', function () {
        return view('usuario.dashboard');
    })->name('usuario.dashboard');
    
});

// RUTAS DE ADMINISTRADOR
Route::middleware(['auth', 'role:administrador'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/usuarios', [AdminDashboardController::class, 'usuarios'])->name('usuarios.index');
    Route::get('/usuarios/aprobar', [AdminDashboardController::class, 'aprobarUsuarios'])->name('usuarios.aprobar');
    Route::get('/equipos', [AdminDashboardController::class, 'equipos'])->name('equipos.index');
    Route::get('/eventos/crear', function() {
        return view('admin.eventos.create');
    })->name('eventos.create');
    Route::get('/backup', [AdminDashboardController::class, 'backup'])->name('backup');
    Route::get('/permisos', [AdminDashboardController::class, 'permisos'])->name('permisos');
    Route::get('/reportes/generar', [AdminDashboardController::class, 'generarReporte'])->name('reportes.generar');
});

// RUTAS DE JUEZ
Route::middleware(['auth', 'role:juez'])->prefix('juez')->name('juez.')->group(function () {
    Route::get('/dashboard', function () {
        return view('juez.dashboard');
    })->name('dashboard');
});
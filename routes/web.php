<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\EventosController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


Route::get('/', [AuthController::class, 'showLogin'])->name('login');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login.form');
Route::post('/login-data', [AuthController::class, 'login'])->name('login.submit');
Route::get('/registro', [AuthController::class, 'showRegister'])->name('register.form');
Route::post('/register-data', [AuthController::class, 'register'])->name('register.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/inicio', [InicioController::class, 'index'])->name('inicio.index');

Route::get('/eventos', [EventosController::class, 'index'])->name('eventos.index');

Route::middleware(['auth'])->group(function () {
    
    Route::get('/usuario/dashboard', function () {
        return view('usuario.dashboard');
    })->name('usuario.dashboard');


    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->middleware('role:administrador')->name('admin.dashboard');

    Route::get('/juez/dashboard', function () {
        return view('juez.dashboard');
    })->middleware('role:juez')->name('juez.dashboard');

});



Route::get('/test', function () {
    return 'Laravel está funcionando correctamente!';
});

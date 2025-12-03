<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event; // Importante: Importar el modelo

class EventosController extends Controller
{
    /**
     * Muestra la página de eventos/torneos (Index)
     */
    public function index()
    {
        // Obtenemos todos los eventos de la BD en lugar del array estático
        $eventos = Event::all(); 
        
        return view('eventos.index', compact('eventos'));
    }

    /**
     * Muestra el detalle de un evento específico
     */
    public function show($id)
    {
        // Buscamos el evento o lanzamos error 404 si no existe
        $event = Event::findOrFail($id);
        
        // Retornamos la vista pasando la variable $event
        // Asegúrate de haber creado el archivo: resources/views/eventos/show.blade.php
        return view('eventos.show', compact('event'));
    }
}

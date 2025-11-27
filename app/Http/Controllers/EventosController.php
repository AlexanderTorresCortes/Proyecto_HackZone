<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EventosController extends Controller
{
    /**
     * Muestra la página de eventos/torneos
     */
    public function index()
    {
        $eventos = $this->getEventos();
        return view('eventos.index', compact('eventos'));
    }

    /**
     * Obtiene los datos de los eventos
     */
    private function getEventos()
    {
        return [
            [
                'id' => 1,
                'organizacion' => 'NASA',
                'org_icon' => 'nasa-icon',
                'imagen' => 'concurso4.png',
                'titulo' => 'AI Innovation Challenge 2025',
                'descripcion' => 'Desarrolla soluciones innovadoras usando inteligencia artificial para resolver problemas del mundo real.',
                'fecha' => '15-17 Marzo 2025',
                'fecha_limite' => '10 Marzo 2024',
                'ubicacion' => 'Mexico, Monterey',
                'participantes_actuales' => 150,
                'participantes_max' => 200,
            ],
            [
                'id' => 2,
                'organizacion' => 'INEGI',
                'org_icon' => 'inegi-icon',
                'imagen' => 'concurso5.png',
                'titulo' => 'Tech Tournament 2025',
                'descripcion' => 'Crea tecnologías sostenibles que ayuden a combatir el cambio climático y proteger el medio ambiente.',
                'fecha' => '15-17 Marzo 2025',
                'fecha_limite' => '10 Marzo 2024',
                'ubicacion' => 'Mexico, Monterey',
                'participantes_actuales' => 150,
                'participantes_max' => 200,
            ],
            [
                'id' => 3,
                'organizacion' => 'Microsoft',
                'org_icon' => 'ms-icon',
                'imagen' => 'concurso6.jpg',
                'titulo' => 'Maratón de Programación',
                'descripcion' => 'Desarrolla soluciones innovadoras usando inteligencia artificial para resolver problemas del mundo real.',
                'fecha' => '15-17 Marzo 2025',
                'fecha_limite' => '10 Marzo 2024',
                'ubicacion' => 'Mexico, Monterey',
                'participantes_actuales' => 150,
                'participantes_max' => 200,
            ],
        ];
    }
}

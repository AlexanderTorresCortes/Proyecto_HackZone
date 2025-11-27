<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InicioController extends Controller
{
    /**
     * Muestra la página de inicio
     */
    public function index()
    {
        $slides = $this->getSlides();
        $stats = $this->getStats();

        return view('inicio.index', compact('slides', 'stats'));
    }

    /**
     * Obtiene los datos de los slides del carrusel
     *
     * Para agregar un nuevo anuncio:
     * 1. Sube tu imagen a: public/images/anuncios/
     * 2. Cambia 'imagen' con el nombre de tu archivo
     * 3. Opcionalmente agrega título, descripción y link
     *
     * Tamaño recomendado: 1200 x 450 píxeles
     */
    private function getSlides()
    {
        return [
            [
                'id' => 1,
                'imagen' => 'TorneoInterColegiado.jpg', // Tu imagen
                'titulo' => 'Torneo Intercolegiado',
                'descripcion' => 'Participa en el torneo de programación',
                'link' => '/eventos',
            ],
            [
                'id' => 2,
                // Sube tu segunda imagen y cambia aquí
                'imagen' => 'TorneoHacker.png',
                'titulo' => 'Hackathon 24 Horas',
                'descripcion' => 'Innovación sin parar',
                'link' => '/eventos',
            ],
            [
                'id' => 3,
                // Sube tu tercera imagen y cambia aquí
                'imagen' => 'TorneoCiberseguridad.png',
                'titulo' => 'Workshop IA & ML',
                'descripcion' => 'Aprende sobre inteligencia artificial',
                'link' => '/eventos',
            ],
        ];
    }

    /**
     * Obtiene las estadísticas para mostrar
     */
    private function getStats()
    {
        return [
            [
                'id' => 1,
                'icon' => 'shield',
                'number' => '3',
                'label' => 'Torneos Activos',
            ],
            [
                'id' => 2,
                'icon' => 'users',
                'number' => 'Top',
                'label' => 'Jugadores destacados',
            ],
            [
                'id' => 3,
                'icon' => 'chart',
                'number' => '$231,348',
                'label' => 'En Premio',
            ],
        ];
    }
}

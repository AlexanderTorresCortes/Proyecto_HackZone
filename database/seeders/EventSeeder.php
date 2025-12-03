<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    public function run()
    {
        // Limpiamos la tabla antes de sembrar para no duplicar datos si corres el seeder varias veces
        Event::truncate(); 

        // 1. EVENTO NASA (Ya lo tenías, lo dejo aquí para completar)
        Event::create([
            'titulo' => 'AI Innovation Challenge 2025',
            'organizacion' => 'NASA',
            'org_icon' => 'fa-brands fa-space-awesome', 
            'imagen' => 'concurso4.png', 
            'descripcion_corta' => 'Desarrolla soluciones innovadoras usando inteligencia artificial para resolver problemas del mundo real.',
            'descripcion_larga' => 'El AI Innovation Challenge 2025 es una oportunidad única para desarrolladores y científicos de datos. Durante 48 horas intensas, los participantes trabajarán en equipos para desarrollar aplicaciones de IA de vanguardia.',
            'fecha_inicio' => Carbon::create(2025, 3, 15),
            'fecha_limite_inscripcion' => Carbon::create(2025, 3, 10),
            'ubicacion' => 'Monterrey, México',
            'participantes_actuales' => 150,
            'participantes_max' => 200,
            'requisitos' => ['Python/JS', 'Machine Learning Básico', 'Laptop propia'],
            'premios' => ['1' => '5000', '2' => '1000', '3' => '500'],
            'cronograma' => [
                ['hora' => '09:00', 'actividad' => 'Registro'],
                ['hora' => '10:00', 'actividad' => 'Hackathon Start']
            ],
            'jueces' => [
                ['nombre' => 'Dr. María G.', 'rol' => 'CTO NASA AI', 'tag' => 'ML Expert'],
                ['nombre' => 'Carlos R.', 'rol' => 'Senior Eng.', 'tag' => 'Deep Learning']
            ]
        ]);

        // 2. EVENTO INEGI (Tech Tournament)
        Event::create([
            'titulo' => 'Tech Tournament 2025',
            'organizacion' => 'INEGI',
            'org_icon' => 'fa-solid fa-chart-pie', // Icono genérico para estadística
            'imagen' => 'concurso5.png', 
            'descripcion_corta' => 'Crea tecnologías sostenibles que ayuden a combatir el cambio climático y proteger el medio ambiente.',
            'descripcion_larga' => 'Únete al reto por el planeta. En este torneo buscamos soluciones tecnológicas que utilicen datos geográficos y estadísticos para proponer mejoras en la sostenibilidad ambiental de las ciudades mexicanas.',
            'fecha_inicio' => Carbon::create(2025, 3, 15),
            'fecha_limite_inscripcion' => Carbon::create(2025, 3, 10),
            'ubicacion' => 'Monterrey, México',
            'participantes_actuales' => 85,
            'participantes_max' => 200,
            'requisitos' => [
                'Conocimiento de bases de datos geográficas',
                'Interés en cambio climático',
                'Equipo de 3 personas'
            ],
            'premios' => [
                '1' => '3000 USD',
                '2' => '1500 USD',
                '3' => 'Kit Solar'
            ],
            'cronograma' => [
                ['hora' => '08:00', 'actividad' => 'Desayuno ecológico'],
                ['hora' => '09:00', 'actividad' => 'Presentación de datos INEGI'],
                ['hora' => '20:00', 'actividad' => 'Cierre día 1']
            ],
            'jueces' => [
                ['nombre' => 'Roberto Gil', 'rol' => 'Director de Geografía', 'tag' => 'Big Data'],
                ['nombre' => 'Ana Paula', 'rol' => 'Ecologista', 'tag' => 'Sostenibilidad']
            ]
        ]);

        // 3. EVENTO MICROSOFT (Maratón de Programación)
        Event::create([
            'titulo' => 'Maratón de Programación',
            'organizacion' => 'Microsoft',
            'org_icon' => 'fa-brands fa-microsoft', 
            'imagen' => 'concurso6.jpg', 
            'descripcion_corta' => 'Demuestra tus habilidades algorítmicas y resuelve desafíos complejos en tiempo récord con la nube de Azure.',
            'descripcion_larga' => '¿Eres el más rápido codificando? Participa en el Maratón anual de Microsoft. Tendrás acceso a créditos de Azure y herramientas exclusivas para resolver problemas de lógica, optimización y desarrollo cloud.',
            'fecha_inicio' => Carbon::create(2025, 3, 15),
            'fecha_limite_inscripcion' => Carbon::create(2025, 3, 10),
            'ubicacion' => 'Monterrey, México',
            'participantes_actuales' => 190,
            'participantes_max' => 200,
            'requisitos' => [
                'Dominio de C#, Java o Python',
                'Cuenta de GitHub activa',
                'Laptop con VS Code instalado'
            ],
            'premios' => [
                '1' => 'Surface Pro 9',
                '2' => 'Xbox Series X',
                '3' => 'Suscripción Azure'
            ],
            'cronograma' => [
                ['hora' => '10:00', 'actividad' => 'Keynote Microsoft'],
                ['hora' => '11:00', 'actividad' => 'Inicio del Coding Challenge'],
                ['hora' => '18:00', 'actividad' => 'Revisión de código']
            ],
            'jueces' => [
                ['nombre' => 'Satya N. (Invitado)', 'rol' => 'CEO', 'tag' => 'Visionary'],
                ['nombre' => 'Linus T.', 'rol' => 'Architect', 'tag' => 'Kernel']
            ]
        ]);
    }
}
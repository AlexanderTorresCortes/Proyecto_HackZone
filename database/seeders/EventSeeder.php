<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    public function run()
    {
        // Limpiamos la tabla antes de sembrar
        Event::truncate(); 

        // 1. EVENTO NASA - AI Innovation Challenge
        Event::create([
            'titulo' => 'AI Innovation Challenge 2025',
            'organizacion' => 'NASA',
            'org_icon' => 'fa-brands fa-space-awesome', 
            'imagen' => 'concurso4.png', 
            'descripcion_corta' => 'Desarrolla soluciones innovadoras usando inteligencia artificial para resolver problemas del mundo real.',
            'descripcion_larga' => 'El AI Innovation Challenge 2025 es una oportunidad única para desarrolladores y científicos de datos. Durante 48 horas intensas, los participantes trabajarán en equipos para desarrollar aplicaciones de IA de vanguardia que puedan tener un impacto real en la exploración espacial y la ciencia.',
            'fecha_inicio' => Carbon::create(2025, 3, 15),
            'fecha_limite_inscripcion' => Carbon::create(2025, 3, 10),
            'ubicacion' => 'Monterrey, México',
            'participantes_actuales' => 150,
            'participantes_max' => 200,
            'requisitos' => [
                'Python o JavaScript',
                'Machine Learning Básico',
                'Laptop propia',
                'Trabajo en equipo'
            ],
            'premios' => [
                '1er lugar' => '$5,000 USD + Viaje a NASA',
                '2do lugar' => '$1,000 USD + Curso IA',
                '3er lugar' => '$500 USD + Kit Arduino'
            ],
            'cronograma' => [
                ['hora' => '09:00', 'actividad' => 'Registro y desayuno'],
                ['hora' => '10:00', 'actividad' => 'Hackathon Start - Presentación del reto'],
                ['hora' => '14:00', 'actividad' => 'Comida y networking'],
                ['hora' => '20:00', 'actividad' => 'Workshop: TensorFlow avanzado'],
                ['hora' => '09:00', 'actividad' => 'Día 2 - Presentaciones finales']
            ],
            'jueces' => [
                ['nombre' => 'Dr. María González', 'rol' => 'CTO NASA AI Lab', 'tags' => ['ML Expert', 'Research']],
                ['nombre' => 'Carlos Rodríguez', 'rol' => 'Senior Engineer', 'tags' => ['Deep Learning', 'Computer Vision']]
            ]
        ]);

        // 2. EVENTO INEGI - Tech Tournament
        Event::create([
            'titulo' => 'Tech Tournament 2025',
            'organizacion' => 'INEGI',
            'org_icon' => 'fa-solid fa-chart-pie',
            'imagen' => 'concurso5.png', 
            'descripcion_corta' => 'Crea tecnologías sostenibles que ayuden a combatir el cambio climático y proteger el medio ambiente.',
            'descripcion_larga' => 'Únete al reto por el planeta. En este torneo buscamos soluciones tecnológicas que utilicen datos geográficos y estadísticos para proponer mejoras en la sostenibilidad ambiental de las ciudades mexicanas. Trabajarás con datasets reales de INEGI para crear dashboards, análisis predictivos y herramientas de visualización.',
            'fecha_inicio' => Carbon::create(2025, 4, 20),
            'fecha_limite_inscripcion' => Carbon::create(2025, 4, 10),
            'ubicacion' => 'Ciudad de México',
            'participantes_actuales' => 85,
            'participantes_max' => 150,
            'requisitos' => [
                'Conocimiento de bases de datos geográficas',
                'Python o R para análisis de datos',
                'Interés en cambio climático',
                'Equipo de 3-5 personas'
            ],
            'premios' => [
                '1er lugar' => '$3,000 USD + Publicación en INEGI',
                '2do lugar' => '$1,500 USD + Dataset exclusivo',
                '3er lugar' => 'Kit Solar + Curso GIS'
            ],
            'cronograma' => [
                ['hora' => '08:00', 'actividad' => 'Desayuno ecológico'],
                ['hora' => '09:00', 'actividad' => 'Presentación de datos INEGI disponibles'],
                ['hora' => '10:00', 'actividad' => 'Inicio del desarrollo'],
                ['hora' => '15:00', 'actividad' => 'Workshop: Análisis geoespacial con Python'],
                ['hora' => '20:00', 'actividad' => 'Cierre día 1']
            ],
            'jueces' => [
                ['nombre' => 'Roberto Gil', 'rol' => 'Director de Geografía INEGI', 'tags' => ['Big Data', 'GIS']],
                ['nombre' => 'Ana Paula Sánchez', 'rol' => 'Ecologista y Data Scientist', 'tags' => ['Sostenibilidad', 'Climate Tech']]
            ]
        ]);

        // 3. EVENTO MICROSOFT - Maratón de Programación
        Event::create([
            'titulo' => 'Maratón de Programación 2025',
            'organizacion' => 'Microsoft',
            'org_icon' => 'fa-brands fa-microsoft', 
            'imagen' => 'concurso6.jpg', 
            'descripcion_corta' => 'Demuestra tus habilidades algorítmicas y resuelve desafíos complejos en tiempo récord con la nube de Azure.',
            'descripcion_larga' => '¿Eres el más rápido codificando? Participa en el Maratón anual de Microsoft donde pondrás a prueba tus habilidades de programación competitiva. Tendrás acceso a créditos de Azure y herramientas exclusivas para resolver problemas de lógica, optimización y desarrollo cloud. Este es el evento perfecto para competidores de nivel intermedio a avanzado.',
            'fecha_inicio' => Carbon::create(2025, 5, 10),
            'fecha_limite_inscripcion' => Carbon::create(2025, 5, 1),
            'ubicacion' => 'Guadalajara, México',
            'participantes_actuales' => 190,
            'participantes_max' => 200,
            'requisitos' => [
                'Dominio de C#, Java o Python',
                'Estructuras de datos y algoritmos',
                'Cuenta de GitHub activa',
                'Laptop con VS Code instalado'
            ],
            'premios' => [
                '1er lugar' => 'Surface Pro 9 + Azure Credits',
                '2do lugar' => 'Xbox Series X + Certificación Microsoft',
                '3er lugar' => 'Suscripción Azure por 1 año'
            ],
            'cronograma' => [
                ['hora' => '10:00', 'actividad' => 'Keynote Microsoft - El futuro del desarrollo'],
                ['hora' => '11:00', 'actividad' => 'Inicio del Coding Challenge'],
                ['hora' => '13:00', 'actividad' => 'Comida rápida'],
                ['hora' => '18:00', 'actividad' => 'Revisión de código en vivo'],
                ['hora' => '19:00', 'actividad' => 'Premiación y networking']
            ],
            'jueces' => [
                ['nombre' => 'Satya Nadella (Invitado Especial)', 'rol' => 'CEO Microsoft', 'tags' => ['Visionary', 'Cloud']],
                ['nombre' => 'Linus Torvalds', 'rol' => 'Software Architect', 'tags' => ['Kernel', 'Open Source']],
                ['nombre' => 'Sarah Chen', 'rol' => 'Azure Principal Engineer', 'tags' => ['Cloud Computing', 'DevOps']]
            ]
        ]);
    }
}
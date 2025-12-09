<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Equipo;
use App\Models\Event;
use App\Models\User;
use App\Models\EquipoMiembro;

class EquipoSeeder extends Seeder
{
    public function run(): void
    {
        $eventos = Event::all()->keyBy('titulo');

        $equipos = [
            // EQUIPOS PARA AI INNOVATION CHALLENGE (NASA)
            [
                'nombre' => 'AI Pioneers',
                'descripcion' => 'Equipo especializado en machine learning y deep learning. Tenemos experiencia con TensorFlow, PyTorch y scikit-learn.',
                'ubicacion' => 'Monterrey, NL',
                'torneo' => 'AI Innovation Challenge 2025',
                'acceso' => 'Público',
                'user_id' => 4, // Julian
                'miembros_actuales' => 3,
                'miembros_max' => 5,
                'estado' => 'Reclutando'
            ],
            [
                'nombre' => 'Neural Networks MX',
                'descripcion' => 'Desarrolladores enfocados en Computer Vision y NLP. Buscamos resolver problemas espaciales con IA.',
                'ubicacion' => 'CDMX',
                'torneo' => 'AI Innovation Challenge 2025',
                'acceso' => 'Público',
                'user_id' => 5, // Ana
                'miembros_actuales' => 4,
                'miembros_max' => 5,
                'estado' => 'Reclutando'
            ],
            [
                'nombre' => 'SpaceCode',
                'descripcion' => 'Apasionados por la exploración espacial y la IA. Queremos usar tecnología para avanzar la ciencia.',
                'ubicacion' => 'Monterrey, NL',
                'torneo' => 'AI Innovation Challenge 2025',
                'acceso' => 'Privado',
                'user_id' => 6, // Pedro
                'miembros_actuales' => 5,
                'miembros_max' => 5,
                'estado' => 'Completo'
            ],

            // EQUIPOS PARA TECH TOURNAMENT (INEGI)
            [
                'nombre' => 'GeoTech Warriors',
                'descripcion' => 'Especialistas en análisis geoespacial y visualización de datos. Usamos Python, QGIS y Tableau.',
                'ubicacion' => 'CDMX',
                'torneo' => 'Tech Tournament 2025',
                'acceso' => 'Público',
                'user_id' => 7, // Laura
                'miembros_actuales' => 3,
                'miembros_max' => 5,
                'estado' => 'Reclutando'
            ],
            [
                'nombre' => 'Climate Coders',
                'descripcion' => 'Desarrolladores comprometidos con el medio ambiente. Creamos soluciones tech para combatir el cambio climático.',
                'ubicacion' => 'Guadalajara, JAL',
                'torneo' => 'Tech Tournament 2025',
                'acceso' => 'Público',
                'user_id' => 8, // Miguel
                'miembros_actuales' => 4,
                'miembros_max' => 5,
                'estado' => 'Reclutando'
            ],
            [
                'nombre' => 'Data4Earth',
                'descripcion' => 'Científicos de datos con pasión por la sostenibilidad. Queremos usar Big Data para un futuro verde.',
                'ubicacion' => 'CDMX',
                'torneo' => 'Tech Tournament 2025',
                'acceso' => 'Público',
                'user_id' => 9, // Sofia
                'miembros_actuales' => 2,
                'miembros_max' => 4,
                'estado' => 'Reclutando'
            ],

            // EQUIPOS PARA MARATÓN DE PROGRAMACIÓN (MICROSOFT)
            [
                'nombre' => 'Code Ninjas',
                'descripcion' => 'Competidores experimentados en maratones de programación. Dominamos C++, Python y algoritmos avanzados.',
                'ubicacion' => 'Guadalajara, JAL',
                'torneo' => 'Maratón de Programación 2025',
                'acceso' => 'Privado',
                'user_id' => 10, // David
                'miembros_actuales' => 3,
                'miembros_max' => 3,
                'estado' => 'Completo'
            ],
            [
                'nombre' => 'Algorithm Masters',
                'descripcion' => 'Expertos en estructuras de datos y optimización. Participamos en Codeforces y LeetCode regularmente.',
                'ubicacion' => 'Monterrey, NL',
                'torneo' => 'Maratón de Programación 2025',
                'acceso' => 'Público',
                'user_id' => 11, // Carla
                'miembros_actuales' => 2,
                'miembros_max' => 3,
                'estado' => 'Reclutando'
            ],
            [
                'nombre' => 'Azure Developers',
                'descripcion' => 'Equipo enfocado en cloud computing y desarrollo en Azure. Usamos C# y .NET Core diariamente.',
                'ubicacion' => 'CDMX',
                'torneo' => 'Maratón de Programación 2025',
                'acceso' => 'Público',
                'user_id' => 4, // Julian (segundo equipo)
                'miembros_actuales' => 4,
                'miembros_max' => 5,
                'estado' => 'Reclutando'
            ],
            [
                'nombre' => 'The Debuggers',
                'descripcion' => 'Solucionadores de problemas natos. Nos especializamos en encontrar bugs y optimizar código eficientemente.',
                'ubicacion' => 'Guadalajara, JAL',
                'torneo' => 'Maratón de Programación 2025',
                'acceso' => 'Público',
                'user_id' => 5, // Ana (segundo equipo)
                'miembros_actuales' => 3,
                'miembros_max' => 4,
                'estado' => 'Reclutando'
            ]
        ];

        foreach ($equipos as $equipoData) {
            // Crear el equipo
            $equipo = Equipo::create([
                'nombre' => $equipoData['nombre'],
                'descripcion' => $equipoData['descripcion'],
                'ubicacion' => $equipoData['ubicacion'],
                'acceso' => $equipoData['acceso'],
                'user_id' => $equipoData['user_id'],
                'miembros_actuales' => $equipoData['miembros_actuales'],
                'miembros_max' => $equipoData['miembros_max'],
                'estado' => $equipoData['estado'],
                'event_id' => $eventos[$equipoData['torneo']]->id ?? null
            ]);

            // Actualizar contador de participantes del evento
            if ($equipo->event_id) {
                $evento = Event::find($equipo->event_id);
                $evento->increment('participantes_actuales', $equipoData['miembros_actuales']);
            }

            // Asignar miembros al equipo (el líder siempre es miembro)
            EquipoMiembro::create([
                'equipo_id' => $equipo->id,
                'user_id' => $equipoData['user_id'],
                'rol' => 'líder'
            ]);

            // Agregar miembros adicionales según el número de miembros actuales
            $usuariosDisponibles = User::where('rol', 'usuario')
                ->where('id', '!=', $equipoData['user_id'])
                ->inRandomOrder()
                ->limit($equipoData['miembros_actuales'] - 1)
                ->get();

            foreach ($usuariosDisponibles as $index => $usuario) {
                // Asignar roles variados
                $rolesDisponibles = ['programador', 'diseñador', 'analista', 'tester', 'documentador'];
                $rol = $rolesDisponibles[$index % count($rolesDisponibles)];

                EquipoMiembro::create([
                    'equipo_id' => $equipo->id,
                    'user_id' => $usuario->id,
                    'rol' => $rol
                ]);
            }
        }
    }
}
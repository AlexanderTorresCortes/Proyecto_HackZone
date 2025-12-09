<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\Equipo;
use App\Models\User;
use App\Models\Evaluacion;
use App\Models\Puntuacion;
use App\Models\CriterioEvaluacion;

class EvaluacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jueces = User::where('rol', 'juez')->get();
        $eventos = Event::with('criteriosEvaluacion')->get();

        foreach ($eventos as $evento) {
            // Obtener equipos inscritos en este evento
            $equipos = Equipo::where('event_id', $evento->id)->get();

            if ($equipos->isEmpty()) {
                continue;
            }

            foreach ($jueces as $juez) {
                foreach ($equipos as $equipo) {
                    // Crear la evaluación
                    $evaluacion = Evaluacion::create([
                        'event_id' => $evento->id,
                        'equipo_id' => $equipo->id,
                        'juez_id' => $juez->id,
                        'estado' => 'completada',
                        'comentarios' => $this->generarComentario($equipo->nombre)
                    ]);

                    // Crear puntuaciones para cada criterio
                    foreach ($evento->criteriosEvaluacion as $criterio) {
                        Puntuacion::create([
                            'evaluacion_id' => $evaluacion->id,
                            'criterio_id' => $criterio->id,
                            'puntuacion' => rand(6, 10) // Puntuación aleatoria entre 6 y 10
                        ]);
                    }
                }
            }

            // No creamos evaluaciones pendientes duplicadas ya que todas están completas en el loop anterior
        }
    }

    private function generarComentario($nombreEquipo): string
    {
        $comentarios = [
            "Excelente trabajo por parte de {$nombreEquipo}. La implementación técnica es sólida y el diseño es intuitivo.",
            "Proyecto muy innovador. {$nombreEquipo} demostró gran capacidad de trabajo en equipo y creatividad.",
            "Buena propuesta por parte de {$nombreEquipo}. Hay áreas de mejora en la funcionalidad pero la idea es prometedora.",
            "Impresionante presentación. {$nombreEquipo} logró crear una solución completa y funcional en el tiempo establecido.",
            "{$nombreEquipo} mostró excelente dominio técnico. El proyecto tiene gran potencial de impacto real.",
            "Trabajo destacado. {$nombreEquipo} supo balancear innovación con viabilidad técnica de manera efectiva.",
        ];

        return $comentarios[array_rand($comentarios)];
    }

    private function generarComentarioCriterio($nombreCriterio): string
    {
        $comentarios = [
            'Innovación' => [
                'Propuesta muy original y creativa.',
                'Buena innovación, aunque hay margen de mejora.',
                'Excelente uso de tecnologías emergentes.',
                'Idea innovadora con gran potencial.'
            ],
            'Impacto' => [
                'Alto impacto potencial en el problema planteado.',
                'Buen enfoque hacia la solución del problema.',
                'Impacto medible y bien fundamentado.',
                'Solución con relevancia práctica evidente.'
            ],
            'Diseño UX/UI' => [
                'Interfaz intuitiva y atractiva visualmente.',
                'Buen diseño con experiencia de usuario fluida.',
                'Diseño funcional, podría mejorarse estéticamente.',
                'Excelente trabajo en la parte visual.'
            ],
            'Funcionalidad Técnica' => [
                'Implementación técnica robusta y bien ejecutada.',
                'Código limpio y bien estructurado.',
                'Funcionalidad completa según los requerimientos.',
                'Buena arquitectura técnica.'
            ],
            'Trabajo en Equipo' => [
                'Excelente coordinación y distribución de tareas.',
                'Se nota la buena organización del equipo.',
                'Trabajo colaborativo efectivo.',
                'Gran sinergia entre los miembros del equipo.'
            ]
        ];

        $opcionesCriterio = $comentarios[$nombreCriterio] ?? ['Buen trabajo en este criterio.'];
        return $opcionesCriterio[array_rand($opcionesCriterio)];
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Entrega;
use App\Models\Equipo;
use App\Models\Event;

class EntregaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todos los equipos que están inscritos en eventos
        $equipos = Equipo::whereNotNull('event_id')->get();

        foreach ($equipos as $equipo) {
            // Cada equipo puede tener múltiples versiones de entregas
            $numeroVersiones = rand(1, 3); // Entre 1 y 3 versiones

            for ($version = 1; $version <= $numeroVersiones; $version++) {
                $tipoArchivo = $this->seleccionarTipoArchivo();

                Entrega::create([
                    'equipo_id' => $equipo->id,
                    'event_id' => $equipo->event_id,
                    'user_id' => $equipo->user_id, // El líder del equipo
                    'nombre_archivo' => $this->generarNombreArchivo($equipo->nombre, $tipoArchivo),
                    'ruta_archivo' => "entregas/{$equipo->event_id}/{$equipo->nombre}_v{$version}_{$tipoArchivo}",
                    'tipo_archivo' => $tipoArchivo,
                    'tamaño' => rand(1048576, 52428800), // Entre 1MB y 50MB
                    'version' => $version,
                    'estado' => $this->seleccionarEstado($version, $numeroVersiones),
                    'comentarios' => $this->generarComentarios($version)
                ]);
            }
        }
    }

    private function seleccionarTipoArchivo(): string
    {
        $tipos = ['zip', 'pdf', 'pptx'];
        return $tipos[array_rand($tipos)];
    }

    private function generarNombreArchivo($nombreEquipo, $tipoArchivo): string
    {
        $nombres = [
            'zip' => [
                'Codigo_Fuente_Proyecto.zip',
                'Proyecto_Final.zip',
                'Source_Code.zip',
                'App_Completa.zip'
            ],
            'pdf' => [
                'Documentacion_Tecnica.pdf',
                'Manual_Usuario.pdf',
                'Reporte_Final.pdf',
                'Presentacion_Proyecto.pdf'
            ],
            'pptx' => [
                'Presentacion_Final.pptx',
                'Demo_Proyecto.pptx',
                'Pitch_Deck.pptx',
                'Slides_Hackathon.pptx'
            ]
        ];

        $opcionesNombre = $nombres[$tipoArchivo] ?? ["Proyecto.{$tipoArchivo}"];
        return $opcionesNombre[array_rand($opcionesNombre)];
    }

    private function seleccionarEstado($versionActual, $totalVersiones): string
    {
        // La última versión suele estar pendiente o aprobada
        // Las versiones anteriores suelen estar aprobadas
        if ($versionActual < $totalVersiones) {
            return 'aprobado';
        }

        $estados = ['pendiente', 'aprobado'];
        return $estados[array_rand($estados)];
    }

    private function generarComentarios($version): ?string
    {
        if ($version == 1) {
            $comentarios = [
                'Primera versión del proyecto',
                'Entrega inicial',
                'Version beta para revisión',
                null
            ];
        } else {
            $comentarios = [
                'Correcciones aplicadas según feedback',
                'Version actualizada con mejoras',
                'Se agregaron funcionalidades adicionales',
                'Optimizaciones y corrección de bugs',
                null
            ];
        }

        return $comentarios[array_rand($comentarios)];
    }
}

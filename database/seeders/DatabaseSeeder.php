<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Deshabilitar la verificación de claves foráneas temporalmente
        // Esto permite la limpieza de tablas que tienen relaciones (como Eventos)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Ejecutar seeders en orden
        $this->call([
            UserSeeder::class,      // 1. Crear usuarios primero
            EventSeeder::class,     // 2. Crear eventos, criterios y jueces (necesita users)
            EquipoSeeder::class,    // 3. Crear equipos (necesita users y events)
        ]);

        // 2. Volver a habilitar la verificación de claves foráneas
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('');
        $this->command->info('✅ ¡Base de datos poblada exitosamente!');
        $this->command->info('');
        $this->command->info('📊 Datos creados:');
        $this->command->info('   • Usuarios: ' . \App\Models\User::count());
        $this->command->info('   • Eventos: ' . \App\Models\Event::count());
        $this->command->info('   • Equipos: ' . \App\Models\Equipo::count());
        $this->command->info('');
    }
}
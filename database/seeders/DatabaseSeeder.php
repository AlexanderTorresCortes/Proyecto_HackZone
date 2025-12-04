<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecutar seeders en orden
        // IMPORTANTE: Primero usuarios, luego eventos, luego equipos
        
        $this->call([
            UserSeeder::class,      // 1. Crear usuarios primero
            EventSeeder::class,     // 2. Crear eventos (tus 3 eventos actuales)
            EquipoSeeder::class,    // 3. Crear equipos (necesita users y events)
        ]);

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
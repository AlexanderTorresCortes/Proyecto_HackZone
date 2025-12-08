<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Equipo;
use App\Models\EquipoMiembro;
use App\Models\Chat;
use Illuminate\Support\Facades\Hash;

class EquipoPruebaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuarios de prueba
        $usuarios = [];
        
        $usuarios[] = User::firstOrCreate(
            ['email' => 'leader@test.com'],
            [
                'name' => 'Líder Prueba',
                'username' => 'leader_test',
                'password' => Hash::make('password123'),
                'rol' => 'usuario'
            ]
        );

        $usuarios[] = User::firstOrCreate(
            ['email' => 'dev1@test.com'],
            [
                'name' => 'Desarrollador 1',
                'username' => 'dev1_test',
                'password' => Hash::make('password123'),
                'rol' => 'usuario'
            ]
        );

        $usuarios[] = User::firstOrCreate(
            ['email' => 'dev2@test.com'],
            [
                'name' => 'Desarrollador 2',
                'username' => 'dev2_test',
                'password' => Hash::make('password123'),
                'rol' => 'usuario'
            ]
        );

        $usuarios[] = User::firstOrCreate(
            ['email' => 'designer@test.com'],
            [
                'name' => 'Diseñador UI/UX',
                'username' => 'designer_test',
                'password' => Hash::make('password123'),
                'rol' => 'usuario'
            ]
        );

        $usuarios[] = User::firstOrCreate(
            ['email' => 'tester@test.com'],
            [
                'name' => 'QA Tester',
                'username' => 'tester_test',
                'password' => Hash::make('password123'),
                'rol' => 'usuario'
            ]
        );

        $this->command->info('✓ Usuarios de prueba creados');

        // Crear equipo de prueba
        $equipo = Equipo::firstOrCreate(
            ['nombre' => 'Los Testers'],
            [
                'descripcion' => 'Equipo creado para probar el sistema de chats grupales. Expertos en encontrar bugs y romper cosas 🐛',
                'ubicacion' => 'Ciudad de México',
                'torneo' => 'AI Innovation Challenge 2025',
                'acceso' => 'Público',
                'user_id' => $usuarios[0]->id, // El líder
                'miembros_actuales' => 5,
                'miembros_max' => 5,
                'estado' => 'Completo'
            ]
        );

        $this->command->info("✓ Equipo '{$equipo->nombre}' creado");

        // Agregar miembros al equipo
        $roles = ['Líder', 'Backend Developer', 'Frontend Developer', 'UI/UX Designer', 'QA Tester'];
        
        foreach ($usuarios as $index => $usuario) {
            EquipoMiembro::firstOrCreate(
                [
                    'equipo_id' => $equipo->id,
                    'user_id' => $usuario->id
                ],
                [
                    'rol' => $roles[$index]
                ]
            );
        }

        $this->command->info('✓ Miembros agregados al equipo');

        // Crear chat del equipo
        $chatExistente = Chat::where('tipo', 'equipo')
            ->where('equipo_id', $equipo->id)
            ->first();

        if (!$chatExistente) {
            $chat = Chat::crearChatEquipo($equipo);
            $this->command->info("✓ Chat de equipo creado con {$chat->miembros()->count()} miembros");
        } else {
            $this->command->warn('⚠️  El chat del equipo ya existía');
        }

        // Crear algunos mensajes de ejemplo
        if (!$chatExistente) {
            $mensajes = [
                ['user' => $usuarios[0], 'texto' => '¡Hola equipo! Bienvenidos al chat grupal 🎉'],
                ['user' => $usuarios[1], 'texto' => 'Hola a todos! Listo para trabajar en el backend'],
                ['user' => $usuarios[2], 'texto' => 'Saludos! Aquí el frontend developer 💻'],
                ['user' => $usuarios[3], 'texto' => 'Hey! Ya tengo algunos diseños preparados 🎨'],
                ['user' => $usuarios[4], 'texto' => '¿Alguien más emocionado por romper todo? 😅'],
                ['user' => $usuarios[0], 'texto' => 'Jajaja, vamos a hacer un gran trabajo juntos!'],
            ];

            foreach ($mensajes as $index => $msg) {
                \App\Models\Mensaje::create([
                    'chat_id' => $chat->id,
                    'user_id' => $msg['user']->id,
                    'mensaje' => $msg['texto'],
                    'leido' => false,
                    'created_at' => now()->subMinutes(30 - ($index * 5))
                ]);
            }

            $this->command->info('✓ Mensajes de prueba creados');
        }

        $this->command->info("\n=== RESUMEN ===");
        $this->command->info("Equipo: {$equipo->nombre}");
        $this->command->info("Miembros: {$equipo->miembros()->count()}");
        $this->command->info("\nCredenciales de prueba (todas con password: password123):");
        foreach ($usuarios as $usuario) {
            $this->command->info("  - {$usuario->email}");
        }
    }
}
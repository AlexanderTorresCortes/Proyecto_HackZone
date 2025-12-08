<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Equipo;
use App\Models\Chat;

class CrearChatsEquipo extends Command
{
    protected $signature = 'chats:crear-equipos';
    protected $description = 'Crear chats automáticos para todos los equipos existentes';

    public function handle()
    {
        $this->info('Creando chats para equipos...');

        $equipos = Equipo::with('miembros')->get();
        $creados = 0;
        $omitidos = 0;

        foreach ($equipos as $equipo) {
            // Verificar si ya existe un chat para este equipo
            $chatExistente = Chat::where('tipo', 'equipo')
                ->where('equipo_id', $equipo->id)
                ->first();

            if ($chatExistente) {
                $this->warn("El equipo '{$equipo->nombre}' ya tiene un chat");
                $omitidos++;
                continue;
            }

            // Crear el chat
            $chat = Chat::crearChatEquipo($equipo);
            $this->info("✓ Chat creado para el equipo: {$equipo->nombre}");
            $creados++;
        }

        $this->info("\n=== Resumen ===");
        $this->info("Chats creados: {$creados}");
        $this->info("Chats omitidos (ya existían): {$omitidos}");
        $this->info("Total equipos procesados: " . ($creados + $omitidos));

        return 0;
    }
}
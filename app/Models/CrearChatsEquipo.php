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
            // Debug: mostrar miembros del equipo
            $miembrosIds = $equipo->miembros()->pluck('user_id')->toArray();
            $this->info("📋 Equipo: {$equipo->nombre}");
            $this->info("   Miembros en equipo_miembros: " . count($miembrosIds) . " → [" . implode(', ', $miembrosIds) . "]");

            // Verificar si ya existe un chat para este equipo
            $chatExistente = Chat::where('tipo', 'equipo')
                ->where('equipo_id', $equipo->id)
                ->first();

            if ($chatExistente) {
                $miembrosEnChat = $chatExistente->miembros()->count();
                $this->warn("   ⚠️  Ya existe chat (ID: {$chatExistente->id}) con {$miembrosEnChat} miembros");
                $omitidos++;
                continue;
            }

            // Crear el chat
            $chat = Chat::crearChatEquipo($equipo);
            
            // Verificar cuántos miembros se agregaron
            $miembrosEnChat = $chat->miembros()->count();
            
            if ($miembrosEnChat == count($miembrosIds)) {
                $this->info("   ✅ Chat creado correctamente con {$miembrosEnChat} miembros");
            } else {
                $this->error("   ❌ ERROR: Se esperaban " . count($miembrosIds) . " miembros pero solo se agregaron {$miembrosEnChat}");
            }
            
            $creados++;
        }

        $this->info("\n=== Resumen ===");
        $this->info("Chats creados: {$creados}");
        $this->info("Chats omitidos (ya existían): {$omitidos}");
        $this->info("Total equipos procesados: " . ($creados + $omitidos));

        return 0;
    }
}
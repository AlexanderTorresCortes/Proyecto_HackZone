<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Equipo;
use App\Models\Chat;
use Illuminate\Support\Facades\DB;

class SincronizarMiembrosChat extends Command
{
    protected $signature = 'chats:sincronizar-miembros';
    protected $description = 'Sincroniza los miembros de equipos con sus chats grupales';

    public function handle()
    {
        $this->info('🔄 Sincronizando miembros de equipos con sus chats...');

        $equipos = Equipo::with('miembros')->get();
        $sincronizados = 0;
        $agregados = 0;

        foreach ($equipos as $equipo) {
            // Buscar o crear el chat del equipo
            $chat = Chat::where('tipo', 'equipo')
                ->where('equipo_id', $equipo->id)
                ->first();

            if (!$chat) {
                $this->warn("⚠️  Equipo '{$equipo->nombre}' no tiene chat. Creando...");
                $chat = Chat::crearChatEquipo($equipo);
                $this->info("   ✅ Chat creado");
                $sincronizados++;
                continue;
            }

            // Obtener IDs de miembros del equipo
            $miembrosEquipo = $equipo->miembros()->pluck('user_id')->toArray();
            
            // Obtener IDs de miembros del chat
            $miembrosChat = $chat->miembros()->pluck('user_id')->toArray();

            // Encontrar miembros que faltan en el chat
            $faltantes = array_diff($miembrosEquipo, $miembrosChat);

            if (count($faltantes) > 0) {
                $this->info("\n📋 Equipo: {$equipo->nombre}");
                $this->info("   Miembros en equipo: " . count($miembrosEquipo));
                $this->info("   Miembros en chat: " . count($miembrosChat));
                $this->info("   Faltantes: " . count($faltantes));
                
                // Agregar los miembros faltantes
                foreach ($faltantes as $userId) {
                    $chat->miembros()->attach($userId);
                    $user = \App\Models\User::find($userId);
                    $this->info("   ✅ Agregado: {$user->name} ({$user->email})");
                    $agregados++;
                }
                
                $sincronizados++;
            } else {
                $this->line("✓ Equipo '{$equipo->nombre}' ya está sincronizado");
            }
        }

        $this->info("\n=== RESUMEN ===");
        $this->info("Equipos sincronizados: {$sincronizados}");
        $this->info("Miembros agregados: {$agregados}");
        
        // Mostrar estado actual
        $this->info("\n=== ESTADO ACTUAL ===");
        foreach ($equipos as $equipo) {
            $chat = Chat::where('tipo', 'equipo')
                ->where('equipo_id', $equipo->id)
                ->first();
            
            if ($chat) {
                $miembrosCount = $chat->miembros()->count();
                $this->line("📱 {$equipo->nombre}: {$miembrosCount} miembros en chat");
            }
        }

        return 0;
    }
}
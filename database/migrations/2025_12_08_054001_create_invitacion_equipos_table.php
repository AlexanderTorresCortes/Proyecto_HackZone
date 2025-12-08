<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invitacion_equipos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipo_id')->constrained('equipos')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Usuario invitado
            $table->foreignId('invitado_por')->constrained('users')->onDelete('cascade'); // Líder que envía la invitación
            $table->string('estado')->default('pendiente'); // pendiente, aceptada, rechazada
            $table->text('mensaje')->nullable();
            $table->timestamps();
            
            // Evitar invitaciones pendientes duplicadas
            $table->unique(['equipo_id', 'user_id'], 'unique_pending_invitation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitacion_equipos');
    }
};

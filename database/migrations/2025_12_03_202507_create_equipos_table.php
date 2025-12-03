<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('equipos', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
        $table->text('descripcion')->nullable();
        $table->string('ubicacion')->nullable();
        $table->string('torneo')->nullable();
        $table->string('acceso')->default('Público'); // 'Público' o 'Privado'
        $table->integer('miembros_actuales')->default(1); // Empieza con 1 (el creador)
        $table->integer('miembros_max')->default(5);
        $table->string('estado')->default('Reclutando');
        
        // Relación con el usuario que crea el equipo (importante para "Mis Equipos")
        // Asegúrate de tener usuarios creados o elimina esta línea si no usas login aún
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipos');
    }
};

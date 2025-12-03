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
    Schema::create('events', function (Blueprint $table) {
        $table->id();
        $table->string('titulo');
        $table->string('organizacion');
        $table->string('org_icon')->default('fa-brands fa-google'); // Ejemplo
        $table->string('imagen'); // Ruta de la imagen
        $table->text('descripcion_corta');
        $table->text('descripcion_larga');
        
        // Datos logísticos
        $table->date('fecha_inicio');
        $table->date('fecha_limite_inscripcion');
        $table->string('ubicacion');
        
        // Participantes
        $table->integer('participantes_max');
        $table->integer('participantes_actuales')->default(0);
        
        // Datos complejos (Arrays en JSON)
        $table->json('requisitos'); // Lista de requisitos
        $table->json('premios');    // {1: 5000, 2: 1000, 3: 500}
        $table->json('cronograma'); // [{hora: '10:00', actividad: '...'}, ...]
        $table->json('jueces');     // [{nombre: '...', rol: '...', tags: []}]
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

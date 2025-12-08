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
        Schema::create('evaluaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('equipo_id')->constrained('equipos')->onDelete('cascade');
            $table->foreignId('juez_id')->constrained('users')->onDelete('cascade');
            $table->text('comentarios')->nullable();
            $table->enum('estado', ['pendiente', 'completada'])->default('pendiente');
            $table->timestamps();

            // Un juez solo puede evaluar un equipo una vez por evento
            $table->unique(['event_id', 'equipo_id', 'juez_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluaciones');
    }
};

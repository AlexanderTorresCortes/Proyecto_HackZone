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
        Schema::create('puntuaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluacion_id')->constrained('evaluaciones')->onDelete('cascade');
            $table->foreignId('criterio_id')->constrained('criterios_evaluacion')->onDelete('cascade');
            $table->integer('puntuacion'); // Puntuación de 1 a 10
            $table->timestamps();

            // Una evaluación tiene una puntuación por criterio
            $table->unique(['evaluacion_id', 'criterio_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puntuaciones');
    }
};

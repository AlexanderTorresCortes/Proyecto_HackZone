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
        Schema::create('criterios_evaluacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('nombre'); // Ej: "Innovación", "Diseño UX/UI"
            $table->text('descripcion')->nullable(); // Descripción del criterio
            $table->integer('peso')->default(1); // Peso del criterio en la evaluación (1-10)
            $table->integer('orden')->default(0); // Orden de aparición
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('criterios_evaluacion');
    }
};

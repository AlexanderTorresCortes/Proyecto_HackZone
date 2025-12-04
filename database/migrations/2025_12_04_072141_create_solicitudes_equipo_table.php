<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('solicitudes_equipo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipo_id')->constrained('equipos')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('estado', ['pendiente', 'aceptada', 'rechazada'])->default('pendiente');
            $table->text('mensaje')->nullable();
            $table->timestamps();
        });

        // Tabla para miembros de equipos
        Schema::create('equipo_miembros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipo_id')->constrained('equipos')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('rol')->nullable();
            $table->timestamps();
            
            $table->unique(['equipo_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('equipo_miembros');
        Schema::dropIfExists('solicitudes_equipo');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            // Agregar tipo de chat y referencia al equipo
            $table->enum('tipo', ['privado', 'equipo'])->default('privado')->after('id');
            $table->unsignedBigInteger('equipo_id')->nullable()->after('tipo');
            $table->string('nombre')->nullable()->after('equipo_id');
            
            // Hacer user2_id nullable para chats de equipo
            $table->unsignedBigInteger('user2_id')->nullable()->change();
            
            // Agregar foreign key para equipos
            $table->foreign('equipo_id')->references('id')->on('equipos')->onDelete('cascade');
        });

        // Crear tabla pivot para miembros de chat de equipo
        Schema::create('chat_miembros', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('chat_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('ultimo_leido_at')->nullable();
            $table->timestamps();

            $table->foreign('chat_id')->references('id')->on('chats')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->unique(['chat_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_miembros');
        
        Schema::table('chats', function (Blueprint $table) {
            $table->dropForeign(['equipo_id']);
            $table->dropColumn(['tipo', 'equipo_id', 'nombre']);
        });
    }
};
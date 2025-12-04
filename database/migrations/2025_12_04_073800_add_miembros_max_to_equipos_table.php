<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipos', function (Blueprint $table) {
            // Agregar columna si no existe
            if (!Schema::hasColumn('equipos', 'miembros_max')) {
                $table->integer('miembros_max')->default(6)->after('miembros_actuales');
            }
        });
    }

    public function down(): void
    {
        Schema::table('equipos', function (Blueprint $table) {
            if (Schema::hasColumn('equipos', 'miembros_max')) {
                $table->dropColumn('miembros_max');
            }
        });
    }
};
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'icono',
        'color',
        'lugar',
    ];

    /**
     * Relación muchos a muchos con equipos
     */
    public function equipos()
    {
        return $this->belongsToMany(Equipo::class, 'equipo_badge')
                    ->withPivot('event_id', 'lugar')
                    ->withTimestamps();
    }

    /**
     * Relación muchos a muchos con usuarios
     */
    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'user_badge')
                    ->withPivot('equipo_id', 'event_id')
                    ->withTimestamps();
    }

    /**
     * Crear o obtener insignia para un lugar específico
     */
    public static function obtenerInsigniaPorLugar($lugar)
    {
        $nombres = [
            1 => 'Campeón',
            2 => 'Subcampeón',
            3 => 'Tercer Lugar',
        ];

        $iconos = [
            1 => 'fas fa-trophy',
            2 => 'fas fa-medal',
            3 => 'fas fa-award',
        ];

        $colores = [
            1 => '#FFD700', // Oro
            2 => '#C0C0C0', // Plata
            3 => '#CD7F32', // Bronce
        ];

        return self::firstOrCreate(
            ['lugar' => $lugar],
            [
                'nombre' => $nombres[$lugar] ?? "Lugar #{$lugar}",
                'descripcion' => "Insignia por obtener el {$lugar}° lugar en un evento",
                'icono' => $iconos[$lugar] ?? 'fas fa-star',
                'color' => $colores[$lugar] ?? '#6B7280',
            ]
        );
    }
}

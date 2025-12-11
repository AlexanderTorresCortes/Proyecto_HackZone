<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificado extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'equipo_id',
        'event_id',
        'lugar',
        'promedio',
    ];

    protected $casts = [
        'promedio' => 'decimal:2',
    ];

    /**
     * Relación con el usuario
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con el equipo
     */
    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }

    /**
     * Relación con el evento
     */
    public function evento()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * Obtener el texto del lugar
     */
    public function getLugarTextoAttribute()
    {
        return match($this->lugar) {
            1 => 'Primer Lugar',
            2 => 'Segundo Lugar',
            3 => 'Tercer Lugar',
            default => 'Lugar ' . $this->lugar,
        };
    }

    /**
     * Obtener el emoji del lugar
     */
    public function getLugarEmojiAttribute()
    {
        return match($this->lugar) {
            1 => '🥇',
            2 => '🥈',
            3 => '🥉',
            default => '🏆',
        };
    }
}

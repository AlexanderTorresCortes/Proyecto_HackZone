<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipoMiembro extends Model
{
    use HasFactory;

    protected $table = 'equipo_miembros';

    protected $fillable = [
        'equipo_id',
        'user_id',
        'rol'
    ];

    /**
     * Relación con el equipo
     */
    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    /**
     * Relación con el usuario
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
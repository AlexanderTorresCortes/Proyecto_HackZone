<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SolicitudEquipo extends Model
{
    use HasFactory;

    protected $table = 'solicitudes_equipo';

    protected $fillable = [
        'equipo_id',
        'user_id',
        'estado',
        'mensaje'
    ];

    // Relación con equipo
    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    // Relación con usuario
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
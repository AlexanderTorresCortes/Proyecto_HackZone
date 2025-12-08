<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitacionEquipo extends Model
{
    use HasFactory;

    protected $table = 'invitacion_equipos';

    protected $fillable = [
        'equipo_id',
        'user_id',
        'invitado_por',
        'estado',
        'mensaje'
    ];

    // Relación con equipo
    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    // Relación con usuario invitado
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relación con el líder que envía la invitación
    public function invitador()
    {
        return $this->belongsTo(User::class, 'invitado_por');
    }

    // Verificar si está pendiente
    public function estaPendiente()
    {
        return $this->estado === 'pendiente';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    // Campos que permitimos llenar desde el formulario
    protected $fillable = [
        'nombre', 
        'descripcion', 
        'ubicacion', 
        'torneo', 
        'acceso',
        'user_id', // Importante para asignar el dueño
        'miembros_actuales',
        'miembros_max'
    ];

    // Relación opcional: Un equipo pertenece a un usuario
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
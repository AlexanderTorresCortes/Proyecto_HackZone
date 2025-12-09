<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entrega extends Model
{
    protected $fillable = [
        'equipo_id',
        'event_id',
        'user_id',
        'nombre_archivo',
        'ruta_archivo',
        'tipo_archivo',
        'tamaño',
        'version',
        'estado',
        'comentarios'
    ];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }

    public function evento()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getFormattedSizeAttribute()
    {
        $bytes = $this->tamaño;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }
}

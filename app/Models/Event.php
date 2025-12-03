<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_limite_inscripcion' => 'date',
        'requisitos' => 'array',
        'premios' => 'array',
        'cronograma' => 'array',
        'jueces' => 'array',
    ];
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'rol',
        'avatar',
        'bio',
        'telefono',
        'ubicacion',
        'habilidades',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'habilidades' => 'array',
        ];
    }

    /**
     * Verificar si el usuario es administrador
     */
    public function isAdmin(): bool
    {
        return $this->rol === 'administrador';
    }

    /**
     * Verificar si el usuario es juez
     */
    public function isJuez(): bool
    {
        return $this->rol === 'juez';
    }

    /**
     * Verificar si el usuario es usuario normal
     */
    public function isUsuario(): bool
    {
        return $this->rol === 'usuario';
    }

    /**
     * Relación muchos a muchos con eventos asignados como juez
     */
    public function eventosComoJuez()
    {
        return $this->belongsToMany(Event::class, 'evento_juez', 'user_id', 'event_id')
                    ->withTimestamps();
    }

    /**
     * Relación con evaluaciones realizadas como juez
     */
    public function evaluacionesRealizadas()
    {
        return $this->hasMany(Evaluacion::class, 'juez_id');
    }

    /**
     * Scope para obtener solo jueces
     */
    public function scopeJueces($query)
    {
        return $query->where('rol', 'juez');
    }

    /**
     * Relación con equipos donde el usuario es líder
     */
    public function equiposComoLider()
    {
        return $this->hasMany(Equipo::class, 'user_id');
    }
}
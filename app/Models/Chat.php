<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chat extends Model
{
    protected $fillable = [
        'user1_id',
        'user2_id',
        'ultimo_mensaje_at'
    ];

    protected $casts = [
        'ultimo_mensaje_at' => 'datetime'
    ];

    /**
     * Relación con el primer usuario
     */
    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    /**
     * Relación con el segundo usuario
     */
    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    /**
     * Relación con los mensajes del chat
     */
    public function mensajes(): HasMany
    {
        return $this->hasMany(Mensaje::class)->orderBy('created_at', 'asc');
    }

    /**
     * Obtener el último mensaje del chat
     */
    public function ultimoMensaje()
    {
        return $this->hasOne(Mensaje::class)->latestOfMany();
    }

    /**
     * Obtener el otro usuario del chat (el que no es el usuario actual)
     */
    public function obtenerOtroUsuario($userId)
    {
        if ($this->user1_id == $userId) {
            return $this->user2;
        }
        return $this->user1;
    }

    /**
     * Obtener mensajes no leídos para un usuario específico
     */
    public function mensajesNoLeidos($userId)
    {
        return $this->mensajes()
            ->where('user_id', '!=', $userId)
            ->where('leido', false)
            ->count();
    }
}

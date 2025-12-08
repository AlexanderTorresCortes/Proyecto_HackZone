<?php

namespace App\Models;
use App\Models\Mensaje;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Chat extends Model
{
    protected $fillable = [
        'tipo',
        'equipo_id',
        'nombre',
        'user1_id',
        'user2_id',
        'ultimo_mensaje_at'
    ];

    protected $casts = [
        'ultimo_mensaje_at' => 'datetime'
    ];

    /**
     * Relación con el primer usuario (para chats privados)
     */
    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    /**
     * Relación con el segundo usuario (para chats privados)
     */
    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    /**
     * Relación con el equipo (para chats de equipo)
     */
    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }

    /**
     * Relación con los miembros del chat (para chats de equipo)
     */
    public function miembros(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_miembros')
            ->withTimestamps()
            ->withPivot('ultimo_leido_at');
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
     * Verificar si el chat es de tipo equipo
     */
    public function esEquipo(): bool
    {
        return $this->tipo === 'equipo';
    }

    /**
     * Verificar si el chat es privado
     */
    public function esPrivado(): bool
    {
        return $this->tipo === 'privado';
    }

    /**
     * Obtener el nombre del chat
     */
    public function obtenerNombre(): string
    {
        if ($this->esEquipo()) {
            return $this->nombre ?? $this->equipo->nombre ?? 'Chat de Equipo';
        }
        
        return 'Chat Privado';
    }

    /**
     * Obtener el otro usuario del chat (solo para chats privados)
     */
    public function obtenerOtroUsuario($userId)
    {
        if ($this->esEquipo()) {
            return null;
        }

        if ($this->user1_id == $userId) {
            return $this->user2;
        }
        return $this->user1;
    }

    /**
     * Verificar si un usuario pertenece al chat
     */
    public function perteneceUsuario($userId): bool
    {
        if ($this->esPrivado()) {
            return $this->user1_id == $userId || $this->user2_id == $userId;
        }
        
        // Para chats de equipo
        return $this->miembros()->where('user_id', $userId)->exists();
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

    /**
     * Crear un chat de equipo
     */
    public static function crearChatEquipo($equipo)
{
    $chat = self::create([
        'tipo' => 'equipo',
        'equipo_id' => $equipo->id,
        'nombre' => 'Chat de ' . $equipo->nombre,
        'ultimo_mensaje_at' => now()
    ]);

    // Agregar todos los miembros del equipo al chat
    $miembros = $equipo->miembros()->pluck('user_id')->toArray();
    $chat->miembros()->attach($miembros);

    return $chat;
}
}

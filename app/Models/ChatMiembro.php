<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMiembro extends Model
{
    protected $table = 'chat_miembros';

    protected $fillable = [
        'chat_id',
        'user_id',
        'ultimo_leido_at'
    ];

    protected $casts = [
        'ultimo_leido_at' => 'datetime'
    ];

    /**
     * Relación con el chat
     */
    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    /**
     * Relación con el usuario
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
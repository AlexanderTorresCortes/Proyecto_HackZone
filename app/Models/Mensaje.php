<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mensaje extends Model
{
    protected $fillable = [
        'chat_id',
        'user_id',
        'mensaje',
        'leido'
    ];

    protected $casts = [
        'leido' => 'boolean',
        'created_at' => 'datetime'
    ];

    /**
     * Relación con el chat al que pertenece
     */
    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    /**
     * Relación con el usuario que envió el mensaje
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

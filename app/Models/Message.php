<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Message extends Model
{
    protected $fillable = [
        'room_id',
        'sender_user_id',
        'body',
        'type',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(
            ChatRoom::class,
            'room_id'
        );
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'sender_user_id'
        );
    }

    public function media(): HasOne
    {
        return $this->hasOne(Media::class);
    }
}

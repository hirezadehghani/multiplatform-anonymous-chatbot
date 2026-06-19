<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Enums\MessageTypeEnum;

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
        'type' => MessageTypeEnum::class,
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

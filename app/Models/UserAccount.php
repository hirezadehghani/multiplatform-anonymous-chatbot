<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\PlatformEnum;

class UserAccount extends Model
{
    protected $fillable = [
        'user_id',
        'bot_id',
        'platform',
        'platform_user_id',
        'username',
        'is_primary',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bot(): BelongsTo
    {
        return $this->belongsTo(Bot::class);
    }

    protected $casts = [
        'platform' => PlatformEnum::class,
        'is_primary' => 'boolean',
    ];
}

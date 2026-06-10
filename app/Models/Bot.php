<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bot extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'platform',
        'token',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function accounts(): HasMany
    {
        return $this->hasMany(UserAccount::class);
    }
}

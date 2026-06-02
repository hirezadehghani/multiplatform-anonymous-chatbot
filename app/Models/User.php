<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use App\Enums\UserStatusEnum;

// #[Fillable(['name', 'email', 'password', 'display_name', 'gender', 'age', 'city', ])]
// #[Hidden([''])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */

    public function accounts(): HasMany
    {
        return $this->hasMany(UserAccount::class);
    }

    public function primaryAccount(): HasOne
    {
        return $this->hasOne(UserAccount::class)
            ->where('is_primary', true);
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function startedRooms(): HasMany
    {
        return $this->hasMany(ChatRoom::class, 'user1_id');
    }

    public function receivedRooms(): HasMany
    {
        return $this->hasMany(ChatRoom::class, 'user2_id');
    }

    public function sentMessages(): HasMany
    {
        return $this->hasMany(
            Message::class,
            'sender_user_id'
        );
    }

    public function referralsMade(): HasMany
    {
        return $this->hasMany(
            Referral::class,
            'referrer_id'
        );
    }

    public function referredBy(): HasOne
    {
        return $this->hasOne(
            Referral::class,
            'referred_user_id'
        );
    }

    public function linkCodes(): HasMany
    {
        return $this->hasMany(LinkCode::class);
    }

    protected $casts = [
        'status' => UserStatusEnum::class,
    ];
}

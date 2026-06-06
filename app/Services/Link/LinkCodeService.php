<?php

namespace App\Services\Link;

use App\Models\LinkCode;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class LinkCodeService
{
    private const EXPIRY_MINUTES = 10;

    public function generateLinkCode(User $user): LinkCode
    {
        return DB::transaction(function () use ($user): LinkCode {
            LinkCode::query()
                ->where('user_id', $user->id)
                ->where('expires_at', '>', now())
                ->delete();

            return LinkCode::query()->create([
                'user_id' => $user->id,
                'code' => $this->generateUniqueCode(),
                'expires_at' => now()->addMinutes(self::EXPIRY_MINUTES),
            ]);
        });
    }

    public function verifyLinkCode(string $code): User
    {
        return DB::transaction(function () use ($code): User {
            $linkCode = LinkCode::query()
                ->where('code', $code)
                ->where('expires_at', '>', now())
                ->lockForUpdate()
                ->first();

            if ($linkCode === null) {
                throw new InvalidArgumentException('Invalid or expired link code.');
            }

            $user = $linkCode->user;
            $linkCode->delete();

            return $user;
        });
    }

    private function generateUniqueCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (LinkCode::query()->where('code', $code)->exists());

        return $code;
    }
}

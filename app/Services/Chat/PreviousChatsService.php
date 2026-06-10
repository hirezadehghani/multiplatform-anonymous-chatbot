<?php

namespace App\Services\Chat;

use App\Enums\ChatRoomStatusEnum;
use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

final class PreviousChatsService
{
    /**
     * Return an array with `data` and `meta` keys containing previous chat rooms for a user.
     * Platform-independent: returns plain arrays, not platform-specific formatting.
     *
     * @return array{data: array, meta: array}
     */
    public function listForUser(User $user, int $perPage = 10, int $page = 1): array
    {
        $query = ChatRoom::query()
            ->where('status', ChatRoomStatusEnum::ENDED)
            ->where(function ($q) use ($user): void {
                $q->where('user1_id', $user->id)
                  ->orWhere('user2_id', $user->id);
            })
            ->withCount('messages')
            ->with(['user1', 'user2'])
            ->orderByDesc('ended_at');

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($perPage, ['*'], 'page', $page);

        $data = $paginator->getCollection()->map(function (ChatRoom $room) use ($user) {
            $partner = $room->user1_id === $user->id ? $room->user2 : $room->user1;

            return [
                'id' => $room->id,
                'partner_id' => $partner->id ?? null,
                'partner_display_name' => $partner->display_name ?? null,
                'messages_count' => $room->messages_count ?? 0,
                'started_at' => $room->started_at?->toDateTimeString(),
                'ended_at' => $room->ended_at?->toDateTimeString(),
            ];
        })->toArray();

        return [
            'data' => $data,
            'meta' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
            ],
        ];
    }
}

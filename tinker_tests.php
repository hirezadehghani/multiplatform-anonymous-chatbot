ChatRoom::query()
    ->where('status', 'ended')
    ->where(function($q) use ($user){
        $q->where('user1_id',$user->id)
          ->orWhere('user2_id',$user->id);
    ChatRoom::query()->where('status', 'ended')->where(function($q) use ($user){$q->where('user1_id',$user->id)->orWhere('user2_id',$user->id);})->count();})->count();

use App\Enums\PlatformEnum;

$messageService->handleIncomingMessage(
    PlatformEnum::BALE,
    '18',
    'hello'
);

$user = App\Models\UserAccount::where('platform_user_id','99999')->first()->user;

ChatRoom::where('user1_id', $user->id)->orWhere('user2_id', $user->id)->get();
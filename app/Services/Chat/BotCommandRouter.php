<?php

namespace App\Services\Chat;

use App\Enums\BotCommand;

class BotCommandRouter
{
    /** @var array<string,BotCommand> */
    private array $map;

    public function __construct()
    {
        $this->map = [
            '/start' => BotCommand::ACCIDENTAL_CHAT,
            '/accidental_chat' => BotCommand::ACCIDENTAL_CHAT,
            '/chat_with_girl' => BotCommand::CHAT_WITH_GIRL,
            '/chat_with_boy' => BotCommand::CHAT_WITH_BOY,
            '/chat_with_goal' => BotCommand::CHAT_WITH_GOAL,
            '/advanced_search' => BotCommand::ADVANCED_SEARCH,
            '/wallet' => BotCommand::WALLET,
            '/invite' => BotCommand::INVITE,
            '/profile' => BotCommand::PROFILE,
            '/previous_chats' => BotCommand::PREVIOUS_CHATS,
        ];

        // Also allow matching by Persian label (lowercased)
        foreach (BotCommand::cases() as $case) {
            $this->map[mb_strtolower($case->label())] = $case;
        }
    }

    public function route(string $incoming): ?BotCommandRoute
    {
        $key = trim(mb_strtolower($incoming));

        if (isset($this->map[$key])) {
            $cmd = $this->map[$key];
            return new BotCommandRoute($cmd, $cmd->label(), $incoming);
        }

        // extract token before space if user sent payload with command
        $first = explode(' ', $key, 2)[0];
        if (isset($this->map[$first])) {
            $cmd = $this->map[$first];
            return new BotCommandRoute($cmd, $cmd->label(), $incoming);
        }

        return null;
    }
}

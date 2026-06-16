<?php

namespace App\Enums;

enum BotCommand: string
{
    case ACCIDENTAL_CHAT = 'accidental_chat';
    case CHAT_WITH_GIRL = 'chat_with_girl';
    case CHAT_WITH_BOY = 'chat_with_boy';
    case CHAT_WITH_GOAL = 'chat_with_goal';
    case ADVANCED_SEARCH = 'advanced_search';
    case WALLET = 'wallet';
    case INVITE = 'invite';
    case PROFILE = 'profile';
    case PREVIOUS_CHATS = 'previous_chats';

    public function label(): string
    {
        return match ($this) {
            self::ACCIDENTAL_CHAT => 'چت تصادفی',
            self::CHAT_WITH_GIRL => 'چت با دختر',
            self::CHAT_WITH_BOY => 'چت با پسر',
            self::CHAT_WITH_GOAL => 'چت هدفمند',
            self::ADVANCED_SEARCH => 'جستجوی پیشرفته',
            self::WALLET => 'کیف پول',
            self::INVITE => 'دعوت از دوستان',
            self::PROFILE => 'پروفایل و آمار',
            self::PREVIOUS_CHATS => 'چت های قبلی',
        };
    }
}

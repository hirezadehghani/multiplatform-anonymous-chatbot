<?php

return [
    // Amount credited to the referrer when a new user redeems their link
    'reward_referrer' => env('REFERRAL_REWARD_REFERRER', 100),

    // Amount credited to the referred user
    'reward_referred' => env('REFERRAL_REWARD_REFERRED', 50),

    // Link time-to-live in days
    'link_ttl_days' => env('REFERRAL_LINK_TTL_DAYS', 30),
];

<?php

namespace App\Services\Chat;

use App\Models\User;

class ProfileCommand
{
    public function __construct(
        private ProfileStateService $stateService,
        private ProfileService $profileService,
    ) {
    }

    /**
     * Handle incoming conversation input for a user's profile flow.
     * The command contains no business logic — it delegates to services.
     */
    public function handle(User $user, string $input): string
    {
        $state = $this->stateService->getState($user);

        switch ($state['step']) {
            case 'start':
                $state['step'] = 'gender';
                $this->stateService->setState($user, $state);
                return 'لطفاً جنسیت خود را وارد کنید (male/female/other)';

            case 'gender':
                $state['data']['gender'] = $input;
                $state['step'] = 'age';
                $this->stateService->setState($user, $state);
                return 'لطفاً سن خود را وارد کنید (عدد)';

            case 'age':
                $state['data']['age'] = (int) $input;
                $state['step'] = 'city';
                $this->stateService->setState($user, $state);
                return 'لطفاً شهر خود را وارد کنید';

            case 'city':
                $state['data']['city'] = $input;
                // delegate storing to ProfileService
                $this->profileService->setGender($user, $state['data']['gender']);
                $this->profileService->setAge($user, $state['data']['age']);
                $this->profileService->setCity($user, $state['data']['city']);
                $this->profileService->completeProfile($user);

                $this->stateService->clearState($user);

                return 'پروفایل شما تکمیل شد — متشکریم!';

            default:
                $this->stateService->clearState($user);
                return 'یک خطای غیرمنتظره رخ داد، دوباره امتحان کنید.';
        }
    }
}

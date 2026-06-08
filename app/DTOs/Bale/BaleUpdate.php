<?php

namespace App\DTOs\Bale;

use InvalidArgumentException;

final readonly class BaleUpdate
{
    /**
     * @param  array<string, mixed>|null  $message
     * @param  array<string, mixed>|null  $editedMessage
     * @param  array<string, mixed>|null  $callbackQuery
     * @param  array<string, mixed>|null  $preCheckoutQuery
     */
    public function __construct(
        public int $updateId,
        public ?array $message = null,
        public ?array $editedMessage = null,
        public ?array $callbackQuery = null,
        public ?array $preCheckoutQuery = null,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromArray(array $payload): self
    {
        if (! isset($payload['update_id'])) {
            throw new InvalidArgumentException('Bale update payload must include update_id.');
        }

        return new self(
            updateId: (int) $payload['update_id'],
            message: isset($payload['message']) ? (array) $payload['message'] : null,
            editedMessage: isset($payload['edited_message']) ? (array) $payload['edited_message'] : null,
            callbackQuery: isset($payload['callback_query']) ? (array) $payload['callback_query'] : null,
            preCheckoutQuery: isset($payload['pre_checkout_query']) ? (array) $payload['pre_checkout_query'] : null,
        );
    }

    /**
     * @return array<string, mixed>|null
     */
    public function sender(): ?array
    {
        if ($this->message !== null) {
            return isset($this->message['from']) ? (array) $this->message['from'] : null;
        }

        if ($this->editedMessage !== null) {
            return isset($this->editedMessage['from']) ? (array) $this->editedMessage['from'] : null;
        }

        if ($this->callbackQuery !== null) {
            return isset($this->callbackQuery['from']) ? (array) $this->callbackQuery['from'] : null;
        }

        if ($this->preCheckoutQuery !== null) {
            return isset($this->preCheckoutQuery['from']) ? (array) $this->preCheckoutQuery['from'] : null;
        }

        return null;
    }

    public function platformUserId(): ?string
    {
        $sender = $this->sender();

        if ($sender === null || ! isset($sender['id'])) {
            return null;
        }

        return (string) $sender['id'];
    }

    public function username(): ?string
    {
        $sender = $this->sender();

        if ($sender === null || ! isset($sender['username'])) {
            return null;
        }

        return (string) $sender['username'];
    }

    public function displayName(): ?string
    {
        $sender = $this->sender();

        if ($sender === null) {
            return null;
        }

        $parts = array_filter([
            $sender['first_name'] ?? null,
            $sender['last_name'] ?? null,
        ]);

        if ($parts !== []) {
            return trim(implode(' ', $parts));
        }

        return $this->username();
    }

    public function text(): ?string
    {
        if ($this->message !== null && isset($this->message['text'])) {
            return (string) $this->message['text'];
        }

        if ($this->editedMessage !== null && isset($this->editedMessage['text'])) {
            return (string) $this->editedMessage['text'];
        }

        if ($this->callbackQuery !== null && isset($this->callbackQuery['data'])) {
            return (string) $this->callbackQuery['data'];
        }

        return null;
    }

    public function hasUserInteraction(): bool
    {
        return $this->platformUserId() !== null;
    }
}

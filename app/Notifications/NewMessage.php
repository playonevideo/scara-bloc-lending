<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Notifications\Notification;

class NewMessage extends Notification
{
    public function __construct(
        public Message $message,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Mesaj nou',
            'message' => "{$this->message->sender->name}: ".mb_strimwidth($this->message->body, 0, 80, '…'),
            'conversation_id' => $this->message->conversation_id,
            'url' => route('conversations.show', $this->message->conversation_id),
        ];
    }
}

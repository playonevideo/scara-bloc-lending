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
        $preview = $this->message->body;

        if (! $preview) {
            $count = $this->message->attachments()->count();
            $preview = $count > 1 ? "📎 A trimis {$count} fișiere" : '📎 A trimis un fișier';
        } else {
            $preview = mb_strimwidth($preview, 0, 80, '…');
        }

        return [
            'title' => 'Mesaj nou',
            'message' => "{$this->message->sender->name}: {$preview}",
            'conversation_id' => $this->message->conversation_id,
            'url' => route('conversations.show', $this->message->conversation_id),
        ];
    }
}

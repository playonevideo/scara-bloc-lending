<?php

namespace App\Livewire;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Notifications\NewMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Chat extends Component
{
    #[Locked]
    public int $conversationId;

    #[Validate('required|string|max:5000')]
    public string $body = '';

    public function mount(Conversation $conversation): void
    {
        abort_unless(
            $conversation->participants()->whereKey(Auth::id())->exists() || Auth::user()->role->isAdmin(),
            403
        );

        $this->conversationId = $conversation->id;
        $this->markRead();
    }

    public function send(): void
    {
        $this->validate();

        $conversation = $this->conversation();

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'body' => trim($this->body),
        ]);

        $this->reset('body');
        $this->markRead();

        if ($other = $conversation->otherParticipant(Auth::user())) {
            $other->notify(new NewMessage($message));
        }
    }

    public function render(): View
    {
        return view('livewire.chat', [
            'conversation' => $this->conversation()->load(['messages.sender', 'object']),
        ]);
    }

    private function conversation(): Conversation
    {
        return Conversation::findOrFail($this->conversationId);
    }

    private function markRead(): void
    {
        Message::query()
            ->where('conversation_id', $this->conversationId)
            ->where('sender_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        ConversationParticipant::query()
            ->where('conversation_id', $this->conversationId)
            ->where('user_id', Auth::id())
            ->update(['last_read_at' => now()]);
    }
}

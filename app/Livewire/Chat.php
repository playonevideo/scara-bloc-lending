<?php

namespace App\Livewire;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\User;
use App\Notifications\NewMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

class Chat extends Component
{
    use WithFileUploads;

    #[Locked]
    public int $conversationId;

    public string $body = '';

    public $attachment = null;

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
        $this->validate([
            'body' => ['nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:5120', 'mimes:jpg,jpeg,png,gif,webp,pdf,zip,doc,docx,txt'],
        ]);

        $body = trim($this->body);

        if ($body === '' && ! $this->attachment) {
            $this->addError('body', 'Scrie un mesaj sau atașează un fișier.');

            return;
        }

        $conversation = $this->conversation();
        $other = $conversation->otherParticipant(Auth::user());

        if ($other && (Auth::user()->isBlocking($other) || Auth::user()->isBlockedBy($other))) {
            $this->addError('body', 'Nu poți trimite mesaje acestui utilizator.');

            return;
        }

        $attachmentPath = null;
        $attachmentName = null;

        if ($this->attachment) {
            $attachmentPath = $this->attachment->store('chat', 'public');
            $attachmentName = $this->attachment->getClientOriginalName();
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'body' => $body,
            'attachment' => $attachmentPath,
            'attachment_name' => $attachmentName,
        ]);

        $this->reset('body', 'attachment');
        $this->markRead();

        if ($other) {
            $other->notify(new NewMessage($message));
        }
    }

    public function deleteMessage(int $messageId): void
    {
        $message = Message::find($messageId);

        if (! $message || ($message->sender_id !== Auth::id() && ! Auth::user()->role->isAdmin())) {
            return;
        }

        if ($message->attachment) {
            Storage::disk('public')->delete($message->attachment);
        }

        $message->delete();
    }

    public function toggleBlock(): void
    {
        $other = $this->conversation()->otherParticipant(Auth::user());

        if (! $other) {
            return;
        }

        if (Auth::user()->isBlocking($other)) {
            Auth::user()->blocks()->where('blocked_id', $other->id)->delete();
        } else {
            Auth::user()->blocks()->create(['blocked_id' => $other->id]);
        }
    }

    public function render(): View
    {
        $conversation = $this->conversation()->load(['messages.sender', 'object', 'participants']);
        $other = $conversation->otherParticipant(Auth::user());

        return view('livewire.chat', [
            'conversation' => $conversation,
            'other' => $other,
            'isBlocked' => $other
                ? (Auth::user()->isBlocking($other) || Auth::user()->isBlockedBy($other))
                : false,
            'isBlocking' => $other ? Auth::user()->isBlocking($other) : false,
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

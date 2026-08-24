<?php

namespace App\Livewire;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\Message;
use App\Models\MessageAttachment;
use App\Notifications\NewMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithFileUploads;

class Messaging extends Component
{
    use WithFileUploads;

    public ?int $activeId = null;

    public string $body = '';

    public array $attachments = [];

    public function mount(?int $conversationId = null): void
    {
        $this->activeId = $conversationId;
    }

    public function select(int $id): void
    {
        $this->activeId = $id;
        $this->reset('body', 'attachments');
        $this->markRead($id);
    }

    public function send(): void
    {
        $this->validate([
            'body' => ['nullable', 'string', 'max:5000'],
            'attachments.*' => ['file', 'max:5120', 'mimes:jpg,jpeg,png,gif,webp,pdf,zip,doc,docx,txt'],
        ]);

        $body = trim($this->body);

        if ($body === '' && empty($this->attachments)) {
            $this->addError('body', 'Scrie un mesaj sau atașează un fișier.');

            return;
        }

        $conversation = $this->active();
        $other = $conversation?->otherParticipant(Auth::user());

        if (! $conversation || $this->isParticipant($conversation) === false) {
            return;
        }

        if ($other && (Auth::user()->isBlocking($other) || Auth::user()->isBlockedBy($other))) {
            $this->addError('body', 'Nu poți trimite mesaje acestui utilizator.');

            return;
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => Auth::id(),
            'body' => $body,
        ]);

        foreach ($this->attachments as $attachment) {
            $path = $attachment->store('chat', 'public');

            MessageAttachment::create([
                'message_id' => $message->id,
                'path' => $path,
                'name' => $attachment->getClientOriginalName(),
                'mime_type' => $attachment->getMimeType(),
            ]);
        }

        $this->reset('body', 'attachments');
        $this->markRead($conversation->id);

        if ($other) {
            $other->notify(new NewMessage($message));
        }
    }

    public function removeAttachment(int $index): void
    {
        if (isset($this->attachments[$index])) {
            unset($this->attachments[$index]);
            $this->attachments = array_values($this->attachments);
        }
    }

    public function deleteMessage(int $messageId): void
    {
        $message = Message::find($messageId);

        if (! $message || ($message->sender_id !== Auth::id() && ! Auth::user()->role->isAdmin())) {
            return;
        }

        foreach ($message->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->path);
        }

        $message->delete();
    }

    public function toggleBlock(): void
    {
        $other = $this->active()?->otherParticipant(Auth::user());

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
        $user = Auth::user();

        $conversations = $user->conversations()
            ->with(['messages', 'messages.attachments', 'participants'])
            ->get()
            ->filter(fn (Conversation $c) => $c->messages->isNotEmpty())
            ->map(function (Conversation $c) use ($user) {
                $c->setAttribute('unread', $c->messages->where('sender_id', '!=', $user->id)->whereNull('read_at')->count());
                $c->setAttribute('latestMessage', $c->messages->last());

                return $c;
            })
            ->sortByDesc(fn (Conversation $c) => $c->latestMessage?->created_at)
            ->values();

        $active = $this->activeId
            ? Conversation::with(['messages.sender', 'messages.attachments', 'object', 'participants'])->find($this->activeId)
            : null;

        if ($active && ! $this->isParticipant($active)) {
            $active = null;
            $this->activeId = null;
        }

        $other = $active?->otherParticipant($user);

        return view('livewire.messaging', [
            'conversations' => $conversations,
            'active' => $active,
            'other' => $other,
            'isBlocked' => $other ? ($user->isBlocking($other) || $user->isBlockedBy($other)) : false,
            'isBlocking' => $other ? $user->isBlocking($other) : false,
        ]);
    }

    private function active(): ?Conversation
    {
        return $this->activeId ? Conversation::find($this->activeId) : null;
    }

    private function isParticipant(Conversation $conversation): bool
    {
        return $conversation->participants()->whereKey(Auth::id())->exists() || Auth::user()->role->isAdmin();
    }

    private function markRead(int $conversationId): void
    {
        Message::query()
            ->where('conversation_id', $conversationId)
            ->where('sender_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        ConversationParticipant::query()
            ->where('conversation_id', $conversationId)
            ->where('user_id', Auth::id())
            ->update(['last_read_at' => now()]);
    }
}

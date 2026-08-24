<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConversationController extends Controller
{
    public function index(Request $request): View
    {
        return view('conversations.index', ['conversation' => null]);
    }

    public function show(Request $request, Conversation $conversation): View
    {
        $this->authorize('view', $conversation);

        return view('conversations.index', [
            'conversation' => $conversation,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'object_id' => ['nullable', 'integer', 'exists:objects,id'],
        ]);

        $user = $request->user();
        $other = User::findOrFail($request->integer('user_id'));

        if ($other->id === $user->id) {
            return back();
        }

        $conversation = $this->findOrCreate($user, $other, $request->integer('object_id') ?: null);

        return redirect()->route('conversations.show', $conversation);
    }

    public function archive(Request $request, Conversation $conversation): RedirectResponse
    {
        $this->authorize('archive', $conversation);

        ConversationParticipant::query()
            ->where('conversation_id', $conversation->id)
            ->where('user_id', $request->user()->id)
            ->update(['archived_at' => now()]);

        return redirect()->route('conversations.index')->with('status', 'Conversația a fost arhivată.');
    }

    private function findOrCreate(User $a, User $b, ?int $objectId = null): Conversation
    {
        $conversation = Conversation::query()
            ->whereNull('loan_id')
            ->whereHas('participants', fn ($q) => $q->whereKey($a->id))
            ->whereHas('participants', fn ($q) => $q->whereKey($b->id))
            ->first();

        if (! $conversation) {
            $conversation = Conversation::create([
                'object_id' => $objectId,
            ]);

            $conversation->participants()->attach([$a->id, $b->id]);
        }

        return $conversation;
    }
}

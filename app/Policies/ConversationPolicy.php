<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\User;

class ConversationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Conversation $conversation): bool
    {
        return $conversation->participants()->whereKey($user->id)->exists() || $user->role->isAdmin();
    }

    public function send(User $user, Conversation $conversation): bool
    {
        return $this->view($user, $conversation);
    }

    public function archive(User $user, Conversation $conversation): bool
    {
        return $this->view($user, $conversation);
    }
}

<?php

namespace App\Policies;

use App\Models\Item;
use App\Models\User;

class ItemPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Item $item): bool
    {
        return $item->is_published || $user->id === $item->owner_id || $user->role->isAdmin();
    }

    public function create(User $user): bool
    {
        return ! $user->isBlocked();
    }

    public function update(User $user, Item $item): bool
    {
        return $user->id === $item->owner_id || $user->role->isAdmin();
    }

    public function delete(User $user, Item $item): bool
    {
        return $user->id === $item->owner_id || $user->role->isAdmin();
    }
}

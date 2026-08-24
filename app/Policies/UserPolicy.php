<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role->isAdmin();
    }

    public function view(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->role->isAdmin();
    }

    public function update(User $user, User $model): bool
    {
        return $user->id === $model->id || $user->role->isAdmin();
    }

    public function delete(User $user, User $model): bool
    {
        return $user->role->isAdmin() && $user->id !== $model->id;
    }
}

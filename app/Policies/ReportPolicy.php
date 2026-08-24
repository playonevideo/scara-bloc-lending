<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;

class ReportPolicy
{
    public function create(User $user): bool
    {
        return ! $user->isBlocked();
    }

    public function viewAny(User $user): bool
    {
        return $user->role->isAdmin();
    }

    public function update(User $user, Report $report): bool
    {
        return $user->role->isAdmin();
    }
}

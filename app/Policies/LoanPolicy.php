<?php

namespace App\Policies;

use App\Enums\LoanStatus;
use App\Models\Loan;
use App\Models\User;

class LoanPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Loan $loan): bool
    {
        return $this->isParticipant($user, $loan) || $user->role->isAdmin();
    }

    public function create(User $user, Loan $loan): bool
    {
        // A user cannot borrow their own object.
        return $loan->lender_id !== $user->id && ! $user->isBlocked();
    }

    public function accept(User $user, Loan $loan): bool
    {
        return $loan->lender_id === $user->id && $loan->status === LoanStatus::Requested;
    }

    public function refuse(User $user, Loan $loan): bool
    {
        return $this->accept($user, $loan);
    }

    public function cancel(User $user, Loan $loan): bool
    {
        return $loan->borrower_id === $user->id
            && in_array($loan->status, [LoanStatus::Requested, LoanStatus::Accepted], true);
    }

    public function markBorrowed(User $user, Loan $loan): bool
    {
        return $this->isParticipant($user, $loan) && $loan->status === LoanStatus::Accepted;
    }

    public function markReturned(User $user, Loan $loan): bool
    {
        return $this->isParticipant($user, $loan)
            && in_array($loan->status, [LoanStatus::Borrowed, LoanStatus::Overdue], true);
    }

    public function complete(User $user, Loan $loan): bool
    {
        return $this->isParticipant($user, $loan) && $loan->status === LoanStatus::Returned;
    }

    public function review(User $user, Loan $loan): bool
    {
        return $this->isParticipant($user, $loan) && $loan->status === LoanStatus::Completed;
    }

    private function isParticipant(User $user, Loan $loan): bool
    {
        return in_array($user->id, [$loan->borrower_id, $loan->lender_id], true);
    }
}

<?php

namespace App\Services;

use App\Enums\LoanStatus;
use App\Enums\ObjectStatus;
use App\Models\Item;
use App\Models\Loan;
use App\Models\User;
use App\Notifications\LoanNotification;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class LoanService
{
    /**
     * Statuses that actively hold an object and therefore block new requests.
     */
    private const BLOCKING_STATUSES = ['accepted', 'borrowed', 'overdue'];

    public function request(Item $object, User $borrower, Carbon $startsAt, Carbon $endsAt, ?string $message = null): Loan
    {
        $this->assertRequestable($object, $borrower, $startsAt, $endsAt);

        $loan = Loan::create([
            'object_id' => $object->id,
            'borrower_id' => $borrower->id,
            'lender_id' => $object->owner_id,
            'status' => LoanStatus::Requested,
            'message' => $message,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'requested_at' => now(),
        ]);

        $object->owner->notify(new LoanNotification(
            $loan,
            "{$borrower->name} a solicitat împrumutul pentru „{$object->title}”."
        ));

        return $loan;
    }

    public function accept(Loan $loan): void
    {
        $loan->update([
            'status' => LoanStatus::Accepted,
            'responded_at' => now(),
        ]);

        $loan->object->update(['status' => ObjectStatus::Reserved]);

        $loan->borrower->notify(new LoanNotification(
            $loan,
            "Solicitarea ta pentru „{$loan->object->title}” a fost acceptată."
        ));
    }

    public function refuse(Loan $loan, ?string $reason = null): void
    {
        $loan->update([
            'status' => LoanStatus::Refused,
            'responded_at' => now(),
            'refused_reason' => $reason,
        ]);

        $loan->borrower->notify(new LoanNotification(
            $loan,
            "Solicitarea ta pentru „{$loan->object->title}” a fost refuzată."
        ));
    }

    public function cancel(Loan $loan): void
    {
        $wasAccepted = $loan->status === LoanStatus::Accepted;

        $loan->update(['status' => LoanStatus::Cancelled]);

        if ($wasAccepted) {
            $this->releaseObject($loan);
        }

        $loan->otherParty(auth()->user())->notify(new LoanNotification(
            $loan,
            "Împrumutul pentru „{$loan->object->title}” a fost anulat."
        ));
    }

    public function markBorrowed(Loan $loan): void
    {
        $loan->update([
            'status' => LoanStatus::Borrowed,
            'borrowed_at' => now(),
        ]);

        $loan->object->update(['status' => ObjectStatus::Borrowed]);

        $loan->otherParty(auth()->user())->notify(new LoanNotification(
            $loan,
            "Obiectul „{$loan->object->title}” a fost predat."
        ));
    }

    public function markReturned(Loan $loan): void
    {
        $loan->update([
            'status' => LoanStatus::Returned,
            'returned_at' => now(),
        ]);

        $loan->otherParty(auth()->user())->notify(new LoanNotification(
            $loan,
            "Obiectul „{$loan->object->title}” a fost returnat."
        ));
    }

    public function complete(Loan $loan): void
    {
        $loan->update([
            'status' => LoanStatus::Completed,
            'completed_at' => now(),
        ]);

        $this->releaseObject($loan);

        $loan->otherParty(auth()->user())->notify(new LoanNotification(
            $loan,
            "Împrumutul pentru „{$loan->object->title}” a fost finalizat. Puteți lăsa o recenzie."
        ));
    }

    public function hasOverlap(Item $object, Carbon $startsAt, Carbon $endsAt, ?int $excludeLoanId = null): bool
    {
        return $object->loans()
            ->whereIn('status', self::BLOCKING_STATUSES)
            ->when($excludeLoanId, fn ($q) => $q->where('id', '!=', $excludeLoanId))
            ->where(function ($q) use ($startsAt, $endsAt) {
                $q->whereBetween('starts_at', [$startsAt, $endsAt])
                    ->orWhereBetween('ends_at', [$startsAt, $endsAt])
                    ->orWhere(fn ($q2) => $q2
                        ->where('starts_at', '<=', $startsAt)
                        ->where('ends_at', '>=', $endsAt));
            })
            ->exists();
    }

    private function assertRequestable(Item $object, User $borrower, Carbon $startsAt, Carbon $endsAt): void
    {
        if (! $object->is_published) {
            throw ValidationException::withMessages(['object' => 'Acest obiect nu este disponibil.']);
        }

        if ($object->owner_id === $borrower->id) {
            throw ValidationException::withMessages(['object' => 'Nu poți împrumuta propriul obiect.']);
        }

        if ($startsAt->gt($endsAt)) {
            throw ValidationException::withMessages(['ends_at' => 'Data de final trebuie să fie după data de început.']);
        }

        if ($startsAt->lt(now()->startOfDay())) {
            throw ValidationException::withMessages(['starts_at' => 'Data de început nu poate fi în trecut.']);
        }

        $maxDays = (int) $object->max_borrow_days;
        if ($startsAt->diffInDays($endsAt) + 1 > $maxDays) {
            throw ValidationException::withMessages([
                'ends_at' => "Perioada maximă de împrumut este de {$maxDays} zile.",
            ]);
        }

        if ($object->status !== ObjectStatus::Available) {
            throw ValidationException::withMessages(['object' => 'Acest obiect nu mai este disponibil.']);
        }

        if ($this->hasOverlap($object, $startsAt, $endsAt)) {
            throw ValidationException::withMessages([
                'starts_at' => 'Acest obiect este deja rezervat în perioada selectată.',
            ]);
        }

        $duplicate = $object->loans()
            ->where('borrower_id', $borrower->id)
            ->whereIn('status', [LoanStatus::Requested->value, LoanStatus::Accepted->value, LoanStatus::Borrowed->value])
            ->exists();

        if ($duplicate) {
            throw ValidationException::withMessages(['object' => 'Ai deja o solicitare activă pentru acest obiect.']);
        }
    }

    private function releaseObject(Loan $loan): void
    {
        $hasOtherBlocking = $loan->object->loans()
            ->where('id', '!=', $loan->id)
            ->whereIn('status', self::BLOCKING_STATUSES)
            ->exists();

        if (! $hasOtherBlocking) {
            $loan->object->update(['status' => ObjectStatus::Available]);
        }
    }
}

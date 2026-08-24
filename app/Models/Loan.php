<?php

namespace App\Models;

use App\Enums\LoanStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'object_id',
        'borrower_id',
        'lender_id',
        'status',
        'message',
        'starts_at',
        'ends_at',
        'requested_at',
        'responded_at',
        'borrowed_at',
        'returned_at',
        'completed_at',
        'refused_reason',
    ];

    protected function casts(): array
    {
        return [
            'status' => LoanStatus::class,
            'starts_at' => 'date',
            'ends_at' => 'date',
            'requested_at' => 'datetime',
            'responded_at' => 'datetime',
            'borrowed_at' => 'datetime',
            'returned_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function object(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'object_id');
    }

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(User::class, 'borrower_id');
    }

    public function lender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lender_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Whether the loan actively blocks the object (accepted, borrowed or overdue).
     */
    public function blocksObject(): bool
    {
        return $this->status->blocksObject();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [
            LoanStatus::Accepted->value,
            LoanStatus::Borrowed->value,
            LoanStatus::Overdue->value,
        ]);
    }

    public function otherParty(User $user): User
    {
        return $this->borrower_id === $user->id ? $this->lender : $this->borrower;
    }
}

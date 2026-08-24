<?php

namespace App\Models;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'apartment_id',
        'is_blocked',
        'blocked_at',
        'show_apartment',
        'show_floor',
        'show_phone',
        'show_email',
        'last_seen_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'is_blocked' => 'boolean',
            'blocked_at' => 'datetime',
            'show_apartment' => 'boolean',
            'show_floor' => 'boolean',
            'show_phone' => 'boolean',
            'show_email' => 'boolean',
            'last_seen_at' => 'datetime',
        ];
    }

    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }

    public function objects(): HasMany
    {
        return $this->hasMany(Object::class, 'owner_id');
    }

    public function loansAsBorrower(): HasMany
    {
        return $this->hasMany(Loan::class, 'borrower_id');
    }

    public function loansAsLender(): HasMany
    {
        return $this->hasMany(Loan::class, 'lender_id');
    }

    public function conversations(): HasManyThrough
    {
        return $this->hasManyThrough(
            Conversation::class,
            ConversationParticipant::class,
            'user_id',
            'id',
            'id',
            'conversation_id'
        );
    }

    public function reviewsReceived(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function isAdmin(): bool
    {
        return $this->role->isAdmin();
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === Role::SuperAdmin;
    }

    public function isBlocked(): bool
    {
        return $this->is_blocked;
    }

    /**
     * Whether the account can authenticate (not blocked / not deleted).
     */
    public function canAuthenticate(): bool
    {
        return ! $this->is_blocked;
    }

    /**
     * The publicly visible display of the resident's location.
     */
    public function locationLabel(): string
    {
        $parts = [];

        if ($this->show_floor && $this->apartment?->floor) {
            $parts[] = 'Etajul '.$this->apartment->floor->number;
        }

        if ($this->show_apartment && $this->apartment) {
            $parts[] = 'Ap. '.$this->apartment->number;
        }

        return $parts ? implode(' · ', $parts) : 'Locatar';
    }

    /**
     * Average rating received (1-5) as a float, or null when there are no reviews.
     */
    public function averageRating(): ?float
    {
        return $this->reviewsReceived()
            ->selectRaw('AVG(rating) as aggregate')
            ->value('aggregate');
    }
}

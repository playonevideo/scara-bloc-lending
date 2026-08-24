<?php

namespace App\Models;

use App\Enums\Role;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laragear\WebAuthn\Contracts\WebAuthnAuthenticatable;
use Laragear\WebAuthn\WebAuthnAuthentication;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class User extends Authenticatable implements FilamentUser, WebAuthnAuthenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, WebAuthnAuthentication;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'apartment_id',
        'two_factor_enabled',
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
            'two_factor_enabled' => 'boolean',
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
        return $this->hasMany(Item::class, 'owner_id');
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
            'conversation_id',
            'id',
            'id'
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

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin() && ! $this->isBlocked();
    }

    public function webAuthnId(): UuidInterface
    {
        return Uuid::uuid5(Uuid::NAMESPACE_OID, 'user-'.$this->getKey());
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

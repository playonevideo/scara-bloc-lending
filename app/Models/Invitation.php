<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Invitation extends Model
{
    protected $fillable = [
        'invited_by',
        'email',
        'phone',
        'code',
        'token',
        'apartment_id',
        'expires_at',
        'used_at',
        'used_by',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    protected $hidden = ['token'];

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function apartment(): BelongsTo
    {
        return $this->belongsTo(Apartment::class);
    }

    public function usedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'used_by');
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isUsed(): bool
    {
        return $this->used_at !== null;
    }

    public function isUsable(): bool
    {
        return ! $this->isExpired() && ! $this->isUsed();
    }

    protected static function booted(): void
    {
        static::creating(function (Invitation $invitation) {
            $invitation->code ??= strtoupper(Str::random(8));
            $invitation->token ??= Str::random(40);
            $invitation->expires_at ??= now()->addDays(7);
            $invitation->invited_by ??= auth()->id();
        });
    }
}

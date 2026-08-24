<?php

namespace App\Models;

use App\Enums\ObjectCondition;
use App\Enums\ObjectStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Object extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'owner_id',
        'category_id',
        'title',
        'slug',
        'description',
        'condition',
        'status',
        'max_borrow_days',
        'requires_personal_handover',
        'can_leave_at_door',
        'special_conditions',
        'usage_instructions',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'condition' => ObjectCondition::class,
            'status' => ObjectStatus::class,
            'requires_personal_handover' => 'boolean',
            'can_leave_at_door' => 'boolean',
            'is_published' => 'boolean',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ObjectImage::class)->orderBy('sort_order');
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function reports(): MorphMany
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', ObjectStatus::Available->value);
    }

    public function coverImage(): ?ObjectImage
    {
        return $this->images->first();
    }

    public function isAvailable(): bool
    {
        return $this->status === ObjectStatus::Available;
    }

    public function popularity(): int
    {
        return $this->loans()->count();
    }
}

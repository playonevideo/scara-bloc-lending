<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Apartment extends Model
{
    use HasFactory;

    protected $fillable = ['floor_id', 'number'];

    public function floor(): BelongsTo
    {
        return $this->belongsTo(Floor::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function fullLabel(): string
    {
        $floor = $this->floor;
        $staircase = $floor?->staircase;
        $building = $staircase?->building;

        return trim(sprintf(
            '%s %s %s Ap. %s',
            $building?->name ?? '',
            $staircase?->name ?? '',
            $floor ? 'Etajul '.$floor->number : '',
            $this->number
        ));
    }
}

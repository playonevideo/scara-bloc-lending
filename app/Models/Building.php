<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Building extends Model
{
    protected $fillable = ['name', 'address'];

    public function staircases(): HasMany
    {
        return $this->hasMany(Staircase::class);
    }
}

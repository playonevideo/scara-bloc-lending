<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObjectImage extends Model
{
    protected $fillable = ['object_id', 'path', 'sort_order'];

    public function object(): BelongsTo
    {
        return $this->belongsTo(Object::class);
    }
}

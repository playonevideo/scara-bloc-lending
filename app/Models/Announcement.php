<?php

namespace App\Models;

use App\Enums\Role;
use App\Notifications\AnnouncementPublished;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = ['author_id', 'title', 'body', 'published_at'];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    protected static function booted(): void
    {
        static::created(function (Announcement $announcement) {
            User::query()
                ->where('role', Role::Resident->value)
                ->get()
                ->each(fn (User $resident) => $resident->notify(new AnnouncementPublished($announcement)));
        });
    }
}

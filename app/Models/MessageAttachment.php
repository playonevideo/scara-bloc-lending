<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageAttachment extends Model
{
    protected $fillable = ['message_id', 'path', 'name', 'mime_type'];

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    public function isImage(): bool
    {
        return (bool) preg_match('/^image\//', (string) $this->mime_type)
            || (bool) preg_match('/\.(jpe?g|png|gif|webp)$/i', $this->path);
    }
}

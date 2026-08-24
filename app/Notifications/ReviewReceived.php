<?php

namespace App\Notifications;

use App\Models\Review;
use Illuminate\Notifications\Notification;

class ReviewReceived extends Notification
{
    public function __construct(public Review $review) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Recenzie primită',
            'message' => "Ai primit o recenzie de {$this->review->rating} stele de la {$this->review->reviewer->name}.",
            'url' => route('history.index'),
        ];
    }
}

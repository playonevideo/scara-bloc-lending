<?php

namespace App\Notifications;

use App\Models\Loan;
use Illuminate\Notifications\Notification;

class LoanNotification extends Notification
{
    public function __construct(
        public Loan $loan,
        public string $message,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Împrumut',
            'message' => $this->message,
            'loan_id' => $this->loan->id,
            'object_title' => $this->loan->object->title,
            'url' => route('loans.index'),
        ];
    }
}

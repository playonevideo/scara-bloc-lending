<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class AccountSuspended extends Notification
{
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Cont blocat',
            'message' => 'Contul tău a fost blocat de către un administrator. Contactează administrația pentru detalii.',
        ];
    }
}

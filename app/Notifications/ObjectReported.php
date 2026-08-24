<?php

namespace App\Notifications;

use App\Models\Report;
use Illuminate\Notifications\Notification;

class ObjectReported extends Notification
{
    public function __construct(public Report $report)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Raportare nouă',
            'message' => "A fost raportat un obiect: {$this->report->reason->label()}.",
            'url' => '/admin/reports',
        ];
    }
}

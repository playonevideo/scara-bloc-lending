<?php

namespace App\Observers;

use App\Services\AuditService;
use Illuminate\Database\Eloquent\Model;

class AuditingObserver
{
    public function updated(Model $model): void
    {
        if (! $this->shouldAudit()) {
            return;
        }

        $changes = $model->getChanges();

        $old = collect(array_keys($changes))
            ->mapWithKeys(fn ($key) => [$key => $model->getOriginal($key)])
            ->all();

        AuditService::log('updated', $model, $old, $changes);
    }

    public function deleted(Model $model): void
    {
        if (! $this->shouldAudit()) {
            return;
        }

        AuditService::log('deleted', $model, $model->getOriginal());
    }

    private function shouldAudit(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }
}

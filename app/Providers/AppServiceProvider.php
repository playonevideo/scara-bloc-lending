<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::morphMap([
            'object' => \App\Models\Item::class,
            'message' => \App\Models\Message::class,
            'user' => \App\Models\User::class,
        ]);

        \App\Models\User::observe(\App\Observers\AuditingObserver::class);
        \App\Models\Item::observe(\App\Observers\AuditingObserver::class);
        \App\Models\Loan::observe(\App\Observers\AuditingObserver::class);
        \App\Models\Apartment::observe(\App\Observers\AuditingObserver::class);
        \App\Models\Building::observe(\App\Observers\AuditingObserver::class);
        \App\Models\Staircase::observe(\App\Observers\AuditingObserver::class);
        \App\Models\Floor::observe(\App\Observers\AuditingObserver::class);
        \App\Models\Category::observe(\App\Observers\AuditingObserver::class);
        \App\Models\Report::observe(\App\Observers\AuditingObserver::class);
        \App\Models\Announcement::observe(\App\Observers\AuditingObserver::class);

        Gate::before(function ($user, $ability) {
            if ($user?->isSuperAdmin()) {
                return true;
            }
        });
    }
}

<?php

namespace App\Providers;

use App\Models\Announcement;
use App\Models\Apartment;
use App\Models\Building;
use App\Models\Category;
use App\Models\Floor;
use App\Models\Item;
use App\Models\Loan;
use App\Models\Message;
use App\Models\Report;
use App\Models\Staircase;
use App\Models\User;
use App\Observers\AuditingObserver;
use Illuminate\Database\Eloquent\Relations\Relation;
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
            'object' => Item::class,
            'message' => Message::class,
            'user' => User::class,
        ]);

        User::observe(AuditingObserver::class);
        Item::observe(AuditingObserver::class);
        Loan::observe(AuditingObserver::class);
        Apartment::observe(AuditingObserver::class);
        Building::observe(AuditingObserver::class);
        Staircase::observe(AuditingObserver::class);
        Floor::observe(AuditingObserver::class);
        Category::observe(AuditingObserver::class);
        Report::observe(AuditingObserver::class);
        Announcement::observe(AuditingObserver::class);
    }
}

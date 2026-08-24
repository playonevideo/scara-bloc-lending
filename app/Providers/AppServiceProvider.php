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

        Gate::before(function ($user, $ability) {
            if ($user?->isSuperAdmin()) {
                return true;
            }
        });
    }
}

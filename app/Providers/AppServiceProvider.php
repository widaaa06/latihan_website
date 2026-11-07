<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\Branch;
use App\Models\Category;
use App\Models\User;
use App\Observers\ActivityObserver;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
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
        User::observe(ActivityObserver::class);
        Branch::observe(ActivityObserver::class);
        Category::observe(ActivityObserver::class);

        Event::listen(Login::class, function (Login $event): void {
            if (! $event->user instanceof User) {
                return;
            }

            ActivityLog::record(
                'login',
                "Pengguna {$event->user->name} masuk",
                $event->user,
                actorId: $event->user->getKey(),
            );
        });

        Event::listen(Logout::class, function (Logout $event): void {
            if (! $event->user instanceof User) {
                return;
            }

            ActivityLog::record(
                'logout',
                "Pengguna {$event->user->name} keluar",
                $event->user,
                actorId: $event->user->getKey(),
            );
        });
    }
}

<?php

namespace App\Providers;

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
        \App\Models\FromAlpas::observe(\App\Observers\FromAlpasObserver::class);
        \App\Models\FromDtr::observe(\App\Observers\FromDtrObserver::class);

        view()->composer('layouts.sdo', function ($view) {
            if (auth()->check()) {
                $userId = auth()->id();
                $unreadCount = \App\Models\Notification::getUnreadCount($userId);
                $notifications = \App\Models\Notification::forRecipient($userId)
                    ->latest('created_at')
                    ->limit(50)
                    ->get();
                $view->with(compact('unreadCount', 'notifications'));
            }
        });
    }
}

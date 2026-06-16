<?php

namespace App\Providers;

use App\Models\AlertMessage;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Share the current user's unconfirmed alerts with the main layout (bottom banner)
        View::composer('layouts.app', function ($view) {
            $view->with('bottomAlerts', Auth::check()
                ? AlertMessage::unreadFor(Auth::id())
                : new Collection());
        });
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
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
        // cấp quyền cao nhất cho Admin
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('Admin')) {
                return true;
            }
        });
        Schema::defaultStringLength(191);
    }
}

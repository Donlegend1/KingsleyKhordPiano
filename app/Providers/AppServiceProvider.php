<?php

namespace App\Providers;

use App\Http\Controllers\CartController;
use App\Models\Setting;
use Illuminate\Support\Facades\View;
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
        try {
            Setting::applyPaymentConfig();
        } catch (\Throwable) {
            // DB may not be ready during early install/migrate.
        }

        View::composer('layouts.app', function ($view) {
            $view->with('cartCount', CartController::count());
            $view->with('cartPreviewItems', CartController::hydrate());
        });
    }
}

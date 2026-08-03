<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\View;
use App\Models\User;
use App\Models\StoreOrder;

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

        Carbon::setLocale('fa');

        View::composer('layouts.customer', function ($view) {
            $host = request()->getHost();
            $host = str_replace('www.', '', $host);

            $agent = User::where('custom_domain', $host)
                ->where('domain_status', 'approved')
                ->first();

            if ($agent) {
                $brand_name = $agent->brand_name ?? 'پنل کاربری';
                $brand_logo = $agent->brand_logo ? asset('storage/' . $agent->brand_logo) : null;
            } else {
                $brand_name = 'سیمرغ پرو';
                $brand_logo = null;
            }

            // ارسال متغیرها به فایل Blade
            $view->with('brand_name', $brand_name)
                ->with('brand_logo', $brand_logo);


        });


        View::composer('layouts.admin', function ($view) {
            $pendingOrdersCount = StoreOrder::where('status', 'pending')->count();

            $view->with('pendingOrdersCount', $pendingOrdersCount);
        });

    }
}

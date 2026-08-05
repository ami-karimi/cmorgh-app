<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
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
        if (file_exists(app_path('Helper/Helper.php'))) {
            require_once app_path('Helper/Helper.php');
        }
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

            if(Auth::user()){
                $agent = auth()->user()->parentAgent;
            }


            if ($agent) {
                $brand_name = $agent->brand_name ?? 'پنل کاربری';
                $brand_logo = $agent->brand_logo ? asset('storage/' . $agent->brand_logo) : null;
            } else {
                $brand_name = setting('SITE_TITLE', 'همراه سیمرغ');
                $brand_logo = setting('SITE_LOGO');
            }

            // ارسال متغیرها به فایل Blade
            $view->with('brand_name', $brand_name)
                ->with('brand_logo', $brand_logo);


        });


        View::composer('layouts.app', function ($view) {
            $host = request()->getHost();
            $host = str_replace('www.', '', $host);

            $agent = User::where('custom_domain', $host)
                ->where('domain_status', 'approved')
                ->first();
            if(Auth::user()){
                $agent = auth()->user()->parentAgent;
            }

            if ($agent) {
                $brand_name = $agent->brand_name ?? 'پنل کاربری';
                $brand_logo = $agent->brand_logo ? asset('storage/' . $agent->brand_logo) : null;
            } else {
                $brand_name = setting('SITE_TITLE', 'همراه سیمرغ');
                $brand_logo = setting('SITE_LOGO');
            }

            // ارسال متغیرها به فایل Blade
            $view->with('brand_name', $brand_name)
                ->with('brand_logo', $brand_logo);


        });



        View::composer('layouts.store', function ($view) {
            $host = request()->getHost();
            $host = str_replace('www.', '', $host);

            $agent = User::where('custom_domain', $host)
                ->where('domain_status', 'approved')
                ->first();
            if(Auth::user()){
                $agent = auth()->user()->parentAgent;
            }

            if ($agent) {
                $brand_name = $agent->brand_name ?? 'پنل کاربری';
                $brand_logo = $agent->brand_logo ? asset('storage/' . $agent->brand_logo) : null;
            } else {
                $brand_name = setting('SITE_TITLE', 'همراه سیمرغ');
                $brand_logo = setting('SITE_LOGO');
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

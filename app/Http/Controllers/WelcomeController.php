<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\AgentStore;

class WelcomeController extends Controller
{
    public function index(Request $request)
    {
        $host = $request->getHost();
        $host = str_replace('www.', '', $host);

        // بررسی دامنه نماینده
        $agent = User::where('custom_domain', $host)->where('domain_status', 'approved')->first();

        // اگر کاربر لاگین کرده بود، نماینده بالادستی او را در نظر می‌گیریم
        if (Auth::check() && Auth::user()->parentAgent) {
            $agent = Auth::user()->parentAgent;
        }

        $sellerId = null;

        if ($agent) {
            $store = AgentStore::where('user_id', $agent->id)->first();
            if ($store && $store->is_active == 1) {
                $sellerId = $agent->id;
                $storeData = [
                    'brand_name' => $agent->brand_name ?? 'فروشگاه VPN',
                    'logo' => $agent->brand_logo ? asset('storage/' . $agent->brand_logo) : null,
                    'support_link' => $store->support_id ?? '#',
                    'description' => $store->title ?? 'خرید امن و تحویل آنی سرویس',
                ];
            } else {
                // اگر نماینده بود اما فروشگاهش غیرفعال بود
                abort(403, 'این فروشگاه در حال حاضر غیرفعال است.');
            }
        } else {
            // تنظیمات اصلی سایت (سیمرغ)
            $storeData = [
                'brand_name' => setting('SITE_TITLE', 'همراه سیمرغ ایران'),
                'logo' => setting('SITE_LOGO'),
                'support_link' => setting('TELEGRAM_SUPPORT', '#'),
                'description' => setting('SITE_DESCRIPTION', 'پلتفرم جامع مدیریت و فروش سرویس اینترنت آزاد'),
            ];
        }

        // ارسال اطلاعات به فایل Blade
        return view('welcome', compact('storeData', 'sellerId'));
    }
}

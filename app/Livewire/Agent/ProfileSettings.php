<?php

namespace App\Livewire\Agent;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Title('مدیریت حساب | همراه سیمرغ')]
#[Layout('layouts.agent')]
class ProfileSettings extends Component
{
    // متغیرهای تغییر رمز عبور
    public $current_password;
    public $password;
    public $password_confirmation;

    // ۱. متد تغییر رمز عبور
    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'password.confirmed' => 'تکرار رمز عبور مطابقت ندارد.',
            'password.min' => 'رمز عبور باید حداقل ۶ کاراکتر باشد.'
        ]);

        $user = Auth::user();

        // بررسی صحت رمز عبور فعلی
        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'رمز عبور فعلی اشتباه است.');
            return;
        }

        // آپدیت رمز عبور جدید
        $user->forceFill([
            'password' => Hash::make($this->password),
        ])->save();

        $this->reset(['current_password', 'password', 'password_confirmation']);
        session()->flash('success_password', 'رمز عبور حساب شما با موفقیت تغییر کرد.');
    }

    // ۲. متد خروج از سایر دستگاه‌ها
    public function logoutOtherBrowserSessions()
    {
        if (config('session.driver') !== 'database') {
            session()->flash('error_session', 'برای این قابلیت، درایور سشن باید روی database باشد.');
            return;
        }

        // پاک کردن تمام سشن‌های این کاربر به جز سشن فعلی
        DB::table('sessions')
            ->where('user_id', Auth::id())
            ->where('id', '!=', request()->session()->getId())
            ->delete();

        session()->flash('success_session', 'شما با موفقیت از سایر دستگاه‌ها خارج شدید.');
    }

    // ۳. دریافت لیست نشست‌های فعال
    public function getSessionsProperty()
    {
        if (config('session.driver') !== 'database') {
            return collect();
        }

        return DB::table('sessions')
            ->where('user_id', Auth::id())
            ->orderBy('last_activity', 'desc')
            ->get()
            ->map(function ($session) {
                $agent = $this->createAgent($session->user_agent);

                return (object) [
                    'agent' => $agent,
                    'ip_address' => $session->ip_address,
                    'is_current_device' => $session->id === request()->session()->getId(),
                    'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
                ];
            });
    }

    // متد کمکی برای استخراج نام سیستم‌عامل و مرورگر از User-Agent
    private function createAgent($userAgent)
    {
        $platform = 'ناشناخته';
        $browser = 'ناشناخته';
        $isDesktop = true;

        if (preg_match('/windows|macintosh|linux/i', strtolower($userAgent))) {
            $platform = preg_match('/windows/i', $userAgent) ? 'Windows' : (preg_match('/macintosh/i', $userAgent) ? 'macOS' : 'Linux');
        } elseif (preg_match('/android|iphone|ipad/i', strtolower($userAgent))) {
            $isDesktop = false;
            $platform = preg_match('/android/i', $userAgent) ? 'Android' : 'iOS';
        }

        if (preg_match('/edg/i', $userAgent)) $browser = 'Edge';
        elseif (preg_match('/chrome/i', $userAgent)) $browser = 'Chrome';
        elseif (preg_match('/firefox/i', $userAgent)) $browser = 'Firefox';
        elseif (preg_match('/safari/i', $userAgent)) $browser = 'Safari';

        return (object) [
            'is_desktop' => $isDesktop,
            'platform' => $platform,
            'browser' => $browser,
        ];
    }

    public function render()
    {
        return view('livewire.agent.profile-settings', [
            'sessions' => $this->sessions
        ]);
    }
}

<?php
namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Accounts;
use App\Models\Financial;
use Carbon\Carbon;
use Morilog\Jalali\Jalalian;

#[Title('داشبورد مدیریت | همراه سیمرغ')]
#[Layout('layouts.admin')]
class Dashboard extends Component
{
    public function render()
    {
        // ---------------------------------------------------------
        // ۱. آمارهای مالی (محاسبه پرداختی‌ها یا واریزی‌ها)
        // فرض می‌کنیم 'plus' به معنی شارژ کیف پول / واریز به سیستم است.
        // ---------------------------------------------------------
        $todayRevenue = Financial::whereIn('type', ['plus'])
            ->whereDate('created_at', Carbon::today())
            ->where('approved', 1)
            ->sum('price');

        $thisMonthRevenue = Financial::whereIn('type', ['plus'])
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->where('approved', 1)
            ->sum('price');

        $lastMonthRevenue = Financial::whereIn('type', ['plus'])
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->where('approved', 1)
            ->sum('price');

        // ---------------------------------------------------------
        // ۲. آمارهای کاربران و اکانت‌ها
        // ---------------------------------------------------------
        // کاربران آنلاین (معمولاً در سیستم‌های VPN از جدول radacct خوانده می‌شود)
        // اگر وایرگارد دیتابیس جدا دارد، می‌توانید با آن جمع کنید.
        $onlineUsersCount = DB::table('radacct')->whereNull('acctstoptime')->count();

        $totalActiveAccounts = Accounts::where('is_enabled', 1)
            ->where(function($q) {
                $q->whereNull('expire_date')->orWhere('expire_date', '>', now());
            })->count();

        // ۵ کاربر/نماینده آخر ثبت‌نام شده
        $latestUsers = User::latest()->take(5)->get();

        // تراکنش‌های تایید نشده (در انتظار بررسی)
        $pendingTransactions = Financial::where('approved', 0)->latest()->take(5)->get();

        // ۱۰ رخداد آخر سیستم (Activity Logs)
        // اگر مدل ActivityLog دارید استفاده کنید، در غیر این صورت از DB::table
        $latestEvents = \App\Models\UserActivity::latest()->take(10)->get();
        if(\Illuminate\Support\Facades\Schema::hasTable('activity_logs')){
            $latestEvents = DB::table('activity_logs')->latest('id')->take(10)->get();
        }

        // ---------------------------------------------------------
        // ۳. دیتای نمودار (تعداد اکانت ساخته شده در ۶ ماه گذشته)
        // ---------------------------------------------------------
        $chartLabels = [];
        $chartData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            // گرفتن نام ماه شمسی برای لیبل‌ها
            $jalaliMonth = Jalalian::fromCarbon($startOfMonth)->format('%B');
            $chartLabels[] = $jalaliMonth;

            // تعداد اکانت‌های ساخته شده در این ماه
            $count = Accounts::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
            $chartData[] = $count;
        }

        return view('livewire.admin.dashboard', [
            'todayRevenue'        => $todayRevenue,
            'thisMonthRevenue'    => $thisMonthRevenue,
            'lastMonthRevenue'    => $lastMonthRevenue,
            'onlineUsersCount'    => $onlineUsersCount,
            'totalActiveAccounts' => $totalActiveAccounts,
            'latestUsers'         => $latestUsers,
            'pendingTransactions' => $pendingTransactions,
            'latestEvents'        => $latestEvents,
            'chartLabels'         => $chartLabels,
            'chartData'           => $chartData,
        ]);
    }
}

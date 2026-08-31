<?php

namespace App\Livewire\Agent;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\Accounts;
use App\Models\Financial;
use App\Models\AgentHiddenGroups;
use App\Models\Group;
use App\Models\Announcement;
use App\Models\ServiceStatus;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

#[Title('داشبورد نماینده | همراه سیمرغ')]
#[Layout('layouts.agent')]
class Dashboard extends Component
{
    public function getBalanceProperty()
    {
        $userId = Auth::id();

        $plus = Financial::where('for', $userId)
            ->whereIn('type', ['plus'])
            ->where('approved', 1)
            ->sum('price');

        $minus = Financial::where('for', $userId)
            ->where('type', 'minus')
            ->where('approved', 1)
            ->sum('price');

        return $plus - $minus;
    }

    public function render()
    {
        $userId = Auth::id();

        $totalCustomers = User::where('creator', $userId)->where('role', 'customer')->count();
        $totalAccounts = Accounts::where('creator', $userId)->count();
        $activeAccounts = Accounts::where('creator', $userId)->where('is_enabled', 1)->count();

        $totalSpent = Financial::where('for', $userId)
            ->where('type', 'minus')
            ->where('approved', 1)
            ->sum('price');

        $recentTransactions = Financial::where('for', $userId)->latest('id')->take(5)->get();

        $expiringAccounts = Accounts::where('creator', $userId)
            ->where('is_enabled', 1)
            ->whereNotNull('expire_date')
            ->where('expire_date', '>=', now())
            ->where('expire_date', '<=', now()->addDays(7))
            ->orderBy('expire_date', 'asc')
            ->take(5)
            ->get();

        $hiddenGroups = AgentHiddenGroups::
        where('agent_id', $userId)
            ->pluck('group_id')
            ->toArray();

        $availableGroups = Group::where('is_enabled', 1)
            ->whereNotIn('id', $hiddenGroups)
            ->get();

        $discountPercent = auth()->user()->discount_percent ?? 0;

        $announcements = Announcement::where('is_active', 1)
            ->whereIn('target', ['all', 'agents'])
            ->latest()
            ->get();

        $services = ServiceStatus::all();

// --- بخش تولید داده‌های نمودار برای مقایسه ۳۰ روز اخیر و ۳۰ روز قبل ---
        $chartLabels = [];
        $currentMonthSales = [];
        $previousMonthSales = [];

        // دریافت آمار ۳۰ روز (از ۲۹ روز پیش تا امروز)
        for ($i = 29; $i >= 0; $i--) {
            $currentDate = Carbon::today()->subDays($i);
            $previousDate = Carbon::today()->subDays($i + 30); // دقیقاً ۳۰ روز قبل‌تر از روز متناظر

            // برچسب محور X (مثلاً 06/15)
            $chartLabels[] = \Morilog\Jalali\Jalalian::fromCarbon($currentDate)->format('m/d');

            // مبلغ خرید شده در بازه فعلی (۳۰ روز اخیر)
            $currentMonthSales[] = Financial::where('for', $userId)
                ->where('type', 'minus')
                ->where('approved', 1)
                ->whereDate('created_at', $currentDate)
                ->sum('price');

            // مبلغ خرید شده در بازه قبلی (۳۰ روز قبل‌تر)
            $previousMonthSales[] = Financial::where('for', $userId)
                ->where('type', 'minus')
                ->where('approved', 1)
                ->whereDate('created_at', $previousDate)
                ->sum('price');
        }

        return view('livewire.agent.dashboard', [
            'totalCustomers' => $totalCustomers,
            'totalAccounts' => $totalAccounts,
            'activeAccounts' => $activeAccounts,
            'totalSpent' => $totalSpent,
            'recentTransactions' => $recentTransactions,
            'expiringAccounts' => $expiringAccounts,
            'availableGroups' => $availableGroups,
            'discountPercent' => $discountPercent,
            'announcements' => $announcements,
            'services' => $services,
            // ارسال متغیرهای جدید به ویو
            'chartLabels' => $chartLabels,
            'currentMonthSales' => $currentMonthSales,
            'previousMonthSales' => $previousMonthSales,
        ]);
    }
}

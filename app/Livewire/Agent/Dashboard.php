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
            ->whereIn('type', ['plus', 'plus_amn'])
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
        ]);
    }
}

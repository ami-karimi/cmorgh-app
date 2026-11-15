<?php

namespace App\Livewire\Agent;

use App\Models\Accounts;
use App\Models\AgentHiddenGroups;
use App\Models\Announcement;
use App\Models\Financial;
use App\Models\Group;
use App\Models\ServiceStatus;
use App\Models\StoreOrder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('داشبورد نماینده | همراه سیمرغ')]
#[Layout('layouts.agent')]
class Dashboard extends Component
{
    public function getBalanceProperty(): int
    {
        $userId = Auth::id();
        return (int) Financial::where('for', $userId)->where('approved', 1)->whereIn('type', ['plus', 'minus'])
            ->selectRaw("COALESCE(SUM(CASE WHEN type = 'plus' THEN price ELSE -price END), 0) as balance")
            ->value('balance');
    }

    private function orderCost(StoreOrder $order): ?int
    {
        $cost = $order->agent_costs;
        if (is_numeric($cost)) return (int) $cost;
        if (is_array($cost)) {
            foreach (['total', 'cost', 'amount', 'agent_cost'] as $key) {
                if (isset($cost[$key]) && is_numeric($cost[$key])) return (int) $cost[$key];
            }
            return collect($cost)->filter(fn ($value) => is_numeric($value))->sum(fn ($value) => (int) $value) ?: null;
        }
        return null;
    }

    public function render()
    {
        $agentId = Auth::id();
        $today = Carbon::today();
        $monthStart = $today->copy()->startOfMonth();
        $orders = StoreOrder::where('agent_id', $agentId);
        $approvedOrders = (clone $orders)->whereIn('status', ['approved', 'paid']);
        $monthOrders = (clone $approvedOrders)->whereBetween('created_at', [$monthStart, Carbon::now()])->get();

        $monthOrderCount = $monthOrders->count();
        $monthlySales = (int) $monthOrders->sum(fn ($order) => (int) $order->price);
        $monthlyProfit = $monthOrders->sum(function ($order) {
            $cost = $this->orderCost($order);
            return $cost === null ? 0 : max(0, (int) $order->price - $cost);
        });
        $ordersWithCost = $monthOrders->filter(fn ($order) => $this->orderCost($order) !== null)->count();

        $chartLabels = [];
        $chartSales = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i);
            $chartLabels[] = \Morilog\Jalali\Jalalian::fromCarbon($date)->format('m/d');
            $chartSales[] = (int) (clone $approvedOrders)->whereDate('created_at', $date)->sum('price');
        }

        $totalCustomers = User::where('creator', $agentId)->where('role', 'customer')->count();
        $totalAccounts = Accounts::where('creator', $agentId)->count();
        $activeAccounts = Accounts::where('creator', $agentId)->where('is_enabled', 1)->count();
        $expiringAccounts = Accounts::where('creator', $agentId)->where('is_enabled', 1)->whereNotNull('expire_date')
            ->whereBetween('expire_date', [$today, $today->copy()->addDays(7)])->orderBy('expire_date')->take(5)->get();
        $pendingOrders = (clone $orders)->where(function ($query) {
            $query->where('status', 'pending')->orWhere(function ($q) {
                $q->where('status', 'paid')->whereIn('fulfillment_state', ['failed', 'requires_review']);
            });
        })->count();
        $recentOrders = (clone $orders)->with(['user', 'group'])->latest()->take(5)->get();
        $recentTransactions = Financial::where('for', $agentId)->latest('id')->take(5)->get();

        $hiddenGroups = AgentHiddenGroups::where('agent_id', $agentId)->pluck('group_id')->toArray();
        $availableGroups = Group::where('is_enabled', 1)->whereNotIn('id', $hiddenGroups)->get();
        $discountPercent = auth()->user()->discount_percent ?? 0;
        $announcements = Announcement::where('is_active', 1)->whereIn('target', ['all', 'agents'])->latest()->get();
        $services = ServiceStatus::all();

        return view('livewire.agent.dashboard', compact(
            'totalCustomers', 'totalAccounts', 'activeAccounts', 'expiringAccounts', 'pendingOrders',
            'recentOrders', 'recentTransactions', 'availableGroups', 'discountPercent', 'announcements',
            'services', 'chartLabels', 'chartSales', 'monthlySales', 'monthOrderCount', 'monthlyProfit', 'ordersWithCost'
        ));
    }
}

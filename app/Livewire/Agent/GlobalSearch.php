<?php

namespace App\Livewire\Agent;

use Livewire\Component;
use App\Models\User;
use App\Models\Accounts;
use Illuminate\Support\Facades\Auth;

class GlobalSearch extends Component
{
    public $search = '';

    public function render()
    {
        $customers = [];
        $accounts = [];

        // جستجو فقط در صورتی انجام می‌شود که کاربر حداقل 2 حرف تایپ کرده باشد
        if (strlen($this->search) >= 2) {
            $agent = Auth::user();

            // ۱. پیدا کردن آیدی تمام زیرمجموعه‌ها (برای امنیت جستجو)
            $directIds = User::where('creator', $agent->id)->pluck('id')->toArray();
            $subCustomerIds = User::whereIn('creator', $directIds)->pluck('id')->toArray();
            $allowedIds = array_merge([$agent->id], $directIds, $subCustomerIds);

            // ۲. جستجو در مشتریان
            $customers = User::whereIn('creator', [$agent->id, ...$directIds])
                ->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                })
                ->take(5) // نمایش حداکثر ۵ نتیجه
                ->get();

            // ۳. جستجو در اکانت‌های VPN
            $accounts = Accounts::whereIn('creator', $allowedIds)
                ->where('username', 'like', '%' . $this->search . '%')
                ->take(5)
                ->get();
        }

        return view('livewire.agent.global-search', [
            'customers' => $customers,
            'accounts' => $accounts
        ]);
    }
}

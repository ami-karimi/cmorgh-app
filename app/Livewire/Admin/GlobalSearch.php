<?php
namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Accounts;

class GlobalSearch extends Component
{
    public string $search = '';

    public function render()
    {
        $users = collect();
        $resellers = collect();
        $accounts = collect();

        if (mb_strlen(trim($this->search)) >= 2) {
            $searchTerm = '%' . trim($this->search) . '%';

            // ۱. جستجو در کاربران معمولی
            $users = User::query()
                ->where(function ($q) {
                    $q->where('role', 'customer') // در صورتی که نقش کاربر معمولی دارید
                    ->orWhereNull('role');
                })
                ->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', $searchTerm)
                        ->orWhere('email', 'like', $searchTerm)
                        ->orWhere('phone', 'like', $searchTerm);
                })
                ->take(5)
                ->get();

            // ۲. جستجو در نمایندگان
            $resellers = User::query()
                ->whereIn('role', ['agent','sub_agent']) // یا Reseller::query() اگر مدل جداگانه دارید
                ->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', $searchTerm)
                        ->orWhere('email', 'like', $searchTerm)
                        ->orWhere('phone', 'like', $searchTerm);
                })
                ->take(5)
                ->get();

            $accounts = Accounts::query()
                ->where('username', 'like', $searchTerm)
                ->orWhere('name', 'like', $searchTerm)
                ->with('users') // برای نمایش صاحب اکانت
                ->take(5)
                ->get();
        }

        return view('livewire.admin.global-search', [
            'users' => $users,
            'resellers' => $resellers,
            'accounts' => $accounts,
        ]);
    }

    public function clear()
    {
        $this->reset('search');
    }
}

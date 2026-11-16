<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ImpersonationController
{
    public function leave(): RedirectResponse
    {
        $adminId = session('impersonating_admin_id');
        abort_unless($adminId, 403, 'حالت ورود به حساب کاربر فعال نیست.');
        $admin = User::whereKey($adminId)->whereIn('role', ['admin', 'manager'])->firstOrFail();
        session()->forget('impersonating_admin_id');
        Auth::login($admin);
        return redirect()->route('admin.users.index');
    }
}

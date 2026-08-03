<?php
namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ProfileDropdown extends Component
{
    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return $this->redirect('/login', navigate: true);
    }

    public function render()
    {
        return view('components.admin.profile-dropdown');
    }
}

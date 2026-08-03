<?php
namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\LastBack;

#[Title('لیست مدیران و نمایندگان | همراه سیمرغ')]
#[Layout('layouts.admin')]
class ManagerList extends Component
{
    use WithPagination;

    public $search = '';
    public $roleFilter = '';
    public $selectedManagers = [];
    public $selectAll = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => ''],
    ];


    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedManagers = $this->render()->getData()['managers']->pluck('id')->toArray();
        } else {
            $this->selectedManagers = [];
        }
    }

    public function bulkAction($action)
    {
        if (empty($this->selectedManagers)) return;

        $query = User::whereIn('id', $this->selectedManagers);

        if ($action === 'activate') $query->update(['is_active' => true]);
        if ($action === 'deactivate') $query->update(['is_active' => false]);
        if ($action === 'delete') $query->delete();

        $this->selectedManagers = [];
        $this->selectAll = false;
        session()->flash('message', 'عملیات گروهی با موفقیت انجام شد.');
    }


    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }


    public function toggleStatus($userId)
    {


        $user = User::findOrFail($userId);
        $user->update([
            'is_active' => !$user->is_active
        ]);

        session()->flash('message', 'وضعیت کاربر با موفقیت تغییر کرد.');
    }

    public function render()
    {
        $managers = User::query()
            ->whereIn('role', ['manager', 'admin','agent','sub_agent'])
            ->when($this->roleFilter, function ($query) {
                $query->where('role', $this->roleFilter);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.managers-list', [
            'managers' => $managers
        ])->with(['header' => 'مدیریت مدیران و نمایندگان']);
    }
}

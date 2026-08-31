<?php
namespace App\Livewire\Agent;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Financial;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

#[Title('مدیریت زیر نمایندگان | پنل نمایندگی')]
#[Layout('layouts.agent')]
class SubAgentManager extends Component
{
    use WithPagination;

    public $search = '';

    // متغیرهای فرم ساخت و ویرایش
    public $isModalOpen = false;
    public $isWalletModalOpen = false;
    public $editingAgentId = null;

    public $name = '';
    public $username = '';
    public $email = '';
    public $phone = '';
    public $password = '';
    public $is_active = 1;

    // متغیرهای شارژ/کسر کیف پول زیرنماینده
    public $walletAmount = '';
    public $walletType = 'plus';
    public $walletDescription = '';

    // متغیر برای ذخیره اطلاعات زیرنماینده انتخاب شده در مودال کیف پول
    public $selectedAgent = null;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * باز کردن مودال ساخت زیرنماینده جدید
     */
    public function openCreateModal()
    {
        $this->reset(['editingAgentId', 'name', 'username', 'email', 'phone', 'password', 'is_active']);
        $this->is_active = 1;
        $this->isModalOpen = true;
    }

    /**
     * باز کردن مودال ویرایش
     */
    public function openEditModal($agentId)
    {
        $agent = User::where('creator', Auth::id())->findOrFail($agentId);

        $this->editingAgentId = $agent->id;
        $this->name = $agent->name;
        $this->username = $agent->username;
        $this->email = $agent->email;
        $this->phone = $agent->phone;
        $this->is_active = $agent->is_active;
        $this->password = '';

        $this->isModalOpen = true;
    }

    /**
     * ذخیره یا آپدیت زیرنماینده
     */
    public function saveAgent()
    {
        $rules = [
            'name'      => 'required|string|max:255',
            'phone'     => 'required|string|max:20|unique:users,phone,' . $this->editingAgentId,
            'email'     => 'nullable|email|max:255|unique:users,email,' . $this->editingAgentId,
            'is_active' => 'required|boolean',
        ];

        if (!$this->editingAgentId) {
            $rules['password'] = 'required|min:6';
        }

        $this->validate($rules, [
            'name.required'     => 'ورود نام و نام خانوادگی الزامی است.',
            'phone.required'    => 'ورود شماره تماس الزامی است.',
            'phone.unique'      => 'این شماره تماس قبلاً ثبت شده است.',
            'email.email'       => 'فرمت ایمیل وارد شده معتبر نیست.',
            'email.unique'      => 'این ایمیل قبلاً ثبت شده است.',
            'password.required' => 'ورود کلمه عبور الزامی است.',
            'password.min'      => 'کلمه عبور باید حداقل ۶ کاراکتر باشد.',
        ]);

        if ($this->editingAgentId) {
            $agent = User::where('creator', Auth::id())
                ->where('role', 'sub_agent')
                ->findOrFail($this->editingAgentId);

            $data = [
                'name'      => $this->name,
                'phone'     => $this->phone,
                'username'  => $this->phone,
                'email'     => $this->email ?: strtolower($this->phone) . '@subagent.com',
                'is_active' => $this->is_active,
            ];

            if (!empty($this->password)) {
                $data['password'] = Hash::make($this->password);
            }

            $agent->update($data);
            session()->flash('success', 'اطلاعات زیر‌نماینده با موفقیت به‌روزرسانی شد.');
        } else {
            User::create([
                'name'      => $this->name,
                'phone'     => $this->phone,
                'username'  => $this->phone,
                'email'     => $this->email ?: strtolower($this->phone) . '@subagent.com',
                'password'  => Hash::make($this->password),
                'role'      => 'sub_agent',
                'creator'   => Auth::id(),
                'is_active' => $this->is_active,
            ]);

            session()->flash('success', 'زیر‌نماینده جدید با موفقیت ایجاد شد.');
        }

        $this->isModalOpen = false;
        $this->reset(['editingAgentId', 'name', 'phone', 'email', 'password', 'is_active']);
    }

    /**
     * تغییر سریع وضعیت فعال/غیرفعال
     */
    public function toggleStatus($agentId)
    {
        $agent = User::where('creator', Auth::id())->findOrFail($agentId);
        $agent->is_active = !$agent->is_active;
        $agent->save();

        session()->flash('success', 'وضعیت زیر‌نماینده تغییر کرد.');
    }

    /**
     * باز کردن مودال مدیریت کیف پول زیرنماینده
     */
    public function openWalletModal($agentId)
    {
        $this->editingAgentId = $agentId;
        $this->reset(['walletAmount', 'walletDescription']);
        $this->walletType = 'plus';

        // دریافت اطلاعات زیرنماینده برای نمایش در مودال
        $this->selectedAgent = User::where('creator', Auth::id())
            ->where('role', 'sub_agent')
            ->find($agentId);

        $this->isWalletModalOpen = true;
    }

    /**
     * تغییر موجودی کیف پول زیرنماینده (با انتقال از/به کیف پول نماینده)
     */
    public function processWalletTransaction()
    {
        $this->validate([
            'walletAmount' => 'required|numeric|min:1000',
        ], [
            'walletAmount.required' => 'وارد کردن مبلغ الزامی است.',
            'walletAmount.min'      => 'مبلغ باید حداقل ۱,۰۰۰ تومان باشد.',
        ]);

        try {
            DB::transaction(function () {
                $myUser = Auth::user();
                $subAgent = User::where('creator', $myUser->id)->findOrFail($this->editingAgentId);
                $amount = (float)$this->walletAmount;

                if ($this->walletType === 'plus') {
                    // بررسی موجودی نماینده برای شارژ زیرنماینده
                    if ($myUser->balance < $amount) {
                        throw new \Exception('موجودی کیف پول شما برای این شارژ کافی نیست!');
                    }

                    // ۱. شارژ زیرنماینده
                    Financial::create([
                        'creator'     => $myUser->id,
                        'for'         => $subAgent->id,
                        'type'        => 'plus',
                        'price'       => $amount,
                        'description' => 'شارژ دریافتی از نماینده بالادست: ' . $myUser->name . ($this->walletDescription ? " ({$this->walletDescription})" : ''),
                        'approved'    => 1,
                    ]);

                    // ۲. کسر از نماینده
                    Financial::create([
                        'creator'     => $myUser->id,
                        'for'         => $myUser->id,
                        'type'        => 'minus',
                        'price'       => $amount,
                        'description' => 'انتقال شارژ به زیرنماینده (' . $subAgent->name . ')' . ($this->walletDescription ? " ({$this->walletDescription})" : ''),
                        'approved'    => 1,
                    ]);

                } else {
                    // کسر از زیرنماینده ⬅️ برگشت به کیف پول نماینده
                    if ($subAgent->balance < $amount) {
                        throw new \Exception('موجودی زیر‌نماینده کمتر از مبلغ درخواستی جهت کسر است!');
                    }

                    // ۱. کسر از زیرنماینده
                    Financial::create([
                        'creator'     => $myUser->id,
                        'for'         => $subAgent->id,
                        'type'        => 'minus',
                        'price'       => $amount,
                        'description' => 'کسر موجودی توسط نماینده بالادست: ' . $myUser->name . ($this->walletDescription ? " ({$this->walletDescription})" : ''),
                        'approved'    => 1,
                    ]);

                    // ۲. افزودن به نماینده
                    Financial::create([
                        'creator'     => $myUser->id,
                        'for'         => $myUser->id,
                        'type'        => 'plus',
                        'price'       => $amount,
                        'description' => 'بازگشت وجه از زیرنماینده (' . $subAgent->name . ')' . ($this->walletDescription ? " ({$this->walletDescription})" : ''),
                        'approved'    => 1,
                    ]);
                }
            });

            session()->flash('success', 'تراکنش کیف پول زیر‌نماینده با موفقیت انجام شد.');
            $this->isWalletModalOpen = false;
            $this->selectedAgent = null;

        } catch (\Exception $e) {
            $this->addError('walletAmount', $e->getMessage());
        }
    }

    public function render()
    {
        $myId = Auth::id();

        $query = User::where('creator', $myId)
            ->where('role', 'sub_agent')
            ->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('username', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        $subAgents = $query->paginate(12);

        $allSubAgents = User::where('creator', $myId)->where('role', 'sub_agent')->get();

        $stats = [
            'totalCount'   => $allSubAgents->count(),
            'activeCount'  => $allSubAgents->where('is_active', 1)->count(),
            'totalBalance' => $allSubAgents->sum('balance'),
        ];

        return view('livewire.agent.sub-agent-manager', [
            'subAgents' => $subAgents,
            'stats'     => $stats,
        ]);
    }
}

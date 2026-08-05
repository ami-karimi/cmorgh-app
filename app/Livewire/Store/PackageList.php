<?php

namespace App\Livewire\Store;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Group;
use App\Models\User;

class PackageList extends Component
{
    public $selectedProtocol = 'wireguard'; // تب پیش‌فرض روی وایرگارد
    public $sellerId = null; // آیدی نماینده (در صورت وجود)

    // متغیرهای مودال ساخت اکانت (برای سیسکو/OpenVPN)
    public $showModal = false;
    public $selectedPackageId = null;
    public $customUsername = '';
    public $customPassword = '';

    protected $rules = [
        'customUsername' => 'required|alpha_dash|min:4|max:20|unique:accounts,username',
        'customPassword' => 'required|min:6|max:20',
    ];

    public function mount()
    {
        // اگر سیستم همکاری در فروش دارید، می‌توانید آیدی نماینده را از سشن یا آدرس (URL) بگیرید
        // $this->sellerId = request()->query('ref') ?? session('seller_id');
    }

    // تغییر تب‌ها
    public function setProtocol($protocol)
    {
        $this->selectedProtocol =$protocol;
    }

    // کلیک روی دکمه خرید
    public function openBuyModal($packageId)
    {
        $this->reset(['customUsername', 'customPassword']);$this->resetValidation();

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->selectedPackageId =$packageId;

        // اگر کاربر تب سیسکو/OpenVPN را انتخاب کرده بود، مودالِ دریافت یوزرنیم باز شود
        if ($this->selectedProtocol === 'l2tp_openvpn') {$this->showModal = true;
        } else {
            // اگر وایرگارد بود نیازی به یوزرنیم نیست، مستقیم برود برای پرداخت
            $this->proceedToPayment($packageId);
        }
    }

    // انتقال مستقیم به درگاه پرداخت (برای وایرگارد)
    public function proceedToPayment($packageId)
    {
        session()->put('pending_vpn_account', [
            'package_id' => $packageId,
            'username' => null,
            'password' => null
        ]);

        // return redirect()->route('payment.start');
        session()->flash('success', "پکیج وایرگارد آماده انتقال به درگاه است.");
    }

    public function confirmAndPay()
    {
        $this->validate();

        session()->put('pending_vpn_account', [
            'package_id' => $this->selectedPackageId,
            'username' => $this->customUsername,
            'password' => $this->customPassword
        ]);

        // return redirect()->route('payment.start');
        session()->flash('success', "اکانت {$this->customUsername} آماده انتقال به درگاه است.");
        $this->showModal = false;
    }

    public function render()
    {
        // ------------------ منطق فیلتر و قیمت گذاری شما ------------------
        $query = Group::where('is_enabled', 1);

        if ($this->sellerId) {
            $hiddenGroups = DB::table('agent_hidden_groups')->where('agent_id',$this->sellerId)->pluck('group_id')->toArray();
            if (!empty($hiddenGroups)) {
                $query->whereNotIn('id',$hiddenGroups);
            }
        }

        $seller = $this->sellerId ? User::find($this->sellerId) : null;

        $allPlans = $query->get()->map(function($group) use ($seller) {$group->final_sell_price = $group->getSellingPriceFor($seller);
            return $group;
        });

        // ------------------ منطق تشخیص پروتکل بر اساس اسم ------------------
        if ($this->selectedProtocol === 'wireguard') {$packages = $allPlans->filter(function($p) {
            $name = strtolower($p->name);
            return str_contains($name, 'wireguard') || str_contains($name, 'وایرگارد') || str_contains($name, 'wg');
            });
        } elseif ($this->selectedProtocol === 'l2tp_openvpn') {$packages = $allPlans->filter(function($p) {
            $name = strtolower($p->name);
            return !str_contains($name, 'wireguard') && !str_contains($name, 'وایرگارد') && !str_contains($name, 'wg');
        });
        } else {
            $packages =$allPlans;
        }

        return view('livewire.store.package-list', [
            'packages' => $packages
        ]);
    }
}

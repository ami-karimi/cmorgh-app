<?php

namespace App\Livewire\Store;

use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\User;
use App\Models\Group;
use App\Models\AgentStore;
use App\Models\StoreOrder;
use App\Models\Financial;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\AccountProvisioningService;
use Illuminate\Support\Str;
class Storefront extends Component
{
    use WithFileUploads;

    public $storeData = [];
    public $sellerId = null;
    public $bankDetails = null;

    public $step = 1;
    public $selectedProtocol = 'all';
    public $selectedPlan = null;
    public $selectedPlanPrice = 0; // 👈 متغیر جدید برای حفظ قیمت در طول مراحل

    public $name,$phone, $email,$receipt;
    public $paymentMethod = 'receipt';

    public function mount()
    {
        $host = request()->getHost();
        $host = str_replace('www.', '',$host);

        $agent = User::where('custom_domain',$host)->where('domain_status', 'approved')->first();
        if(Auth::user()){
            $agent = auth()->user()->parentAgent;
        }

        if ($agent) {
            $store = AgentStore::where('user_id',$agent->id)->first();
            if ($store &&$store->is_active == 1) {
                $this->sellerId =$agent->id;
                $this->storeData = [
                    'brand_name' => $agent->brand_name ?? 'فروشگاه VPN',
                    'logo' => $agent->brand_logo ? asset('storage/' . $agent->brand_logo) : null,
                    'support_link' => $store->support_id ?? '#',
                    'description' => $store->title ?? 'خرید امن و تحویل آنی سرویس',
                ];
                $this->bankDetails = DB::table('agent_bank_accounts')->where('user_id',$agent->id)->where('is_show', 1)->first();
            } else {
                abort(403, 'این فروشگاه در حال حاضر غیرفعال است.');
            }
        } else {
            $this->storeData = [
                'brand_name' => 'سیمرغ پرو',
                'logo' => null,
                'support_link' => '#',
                'description' => 'پلتفرم جامع مدیریت و فروش سرویس اینترنت آزاد',
            ];
            $mng= User::where('role','manager')->first();
            $this->bankDetails = DB::table('agent_bank_accounts')->where('user_id', $mng->id)->first();
        }

        if (Auth::check()) {
            $this->name = Auth::user()->name;
            $this->phone = Auth::user()->phone;
            $this->email = Auth::user()->email;
            $this->paymentMethod = 'wallet';
        }
    }

    public function selectPlan($planId)
    {
        $this->selectedPlan = Group::findOrFail($planId);

        // 👈 محاسبه قیمت و ذخیره در متغیر مستقل برای جلوگیری از صفر شدن
        $seller = $this->sellerId ? User::find($this->sellerId) : null;
        $this->selectedPlanPrice =$this->selectedPlan->getSellingPriceFor($seller);$this->step = 2;
    }

    public function goToPayment()
    {
        if (!Auth::check()) {
            $this->validate([
                'name'  => 'required|string|max:255',
                'phone' => 'required|numeric|digits:11',
            ], [
                'name.required' => 'وارد کردن نام الزامی است.',
                'phone.required' => 'شماره موبایل جهت دریافت اکانت الزامی است.',
                'phone.digits' => 'شماره موبایل باید ۱۱ رقم باشد.'
            ]);
        }
        $this->step = 3;
    }

    public function previousStep()
    {
        if ($this->step > 1) {$this->step--;
        }
    }

    public function submitOrder()
    {
        $seller = $this->sellerId ? User::find($this->sellerId) : null;
        $securePrice = $this->selectedPlan->getSellingPriceFor($seller);

        // =========================================================================
        // ۱. حالت پرداخت مستقیم و آنی از کیف پول (ساخت فوری اکانت VPN)
        // =========================================================================
        if (Auth::check() && $this->paymentMethod === 'wallet') {
            $user = Auth::user();

            if ($user->balance < $securePrice) {
                $this->addError('wallet', 'موجودی کیف پول شما کافی نیست. لطفاً حساب خود را شارژ کنید یا فیش واریز نمایید.');
                return;
            }

            try {
                DB::transaction(function () use ($user, $securePrice) {

                    // ۱-۲. ایجاد سفارش در وضعیت تایید شده
                    $order = StoreOrder::create([
                        'agent_id'      => $this->sellerId,
                        'user_id'       => $user->id,
                        'group_id'      => $this->selectedPlan->id,
                        'phone'         => $this->phone ?? $user->phone,
                        'email'         => $this->email ?? $user->email,
                        'price'         => $securePrice,
                        'receipt_image' => 'wallet_payment',
                        'status'        => 'approved',
                    ]);

                    // ۱-۳. استفاده از سرویس یکپارچه برای آماده‌سازی دیتا
                    $accService = new \App\Services\AccountProvisioningService();
                    $preparedData = $accService->prepareAccountData($this->selectedPlan, $user, $this->phone ?? null);

                    // ۱-۴. ساخت اکانت و کسر هزینه عمده از نماینده
                    $createdAccount = $accService->createFullAccount(
                        $preparedData['userData'],
                        $preparedData['configData'],
                        $user->id
                    );
                    if (isset($createdAccount['status']) && $createdAccount['status'] === false) {
                        throw new \Exception($createdAccount['message'] ?? 'خطایی در صدور اکانت رخ داد.');
                    }
                    if ($createdAccount) {
                        $order->update(['account_id' => $createdAccount->id]);
                    }
                });

                session()->flash('success_order', 'پرداخت از کیف پول با موفقیت انجام شد و سرویس جدید شما بلافاصله در داشبورد فعال گردید!');
                $this->resetWizard();
                return;

            } catch (\Exception $e) {
                $this->addError('wallet', 'خطا در صدور آنی اکانت: ' . $e->getMessage());
                return;
            }
        }

        // =========================================================================
        // ۲. حالت ثبت با فیش واریزی (در انتظار تایید مدیریت)
        // =========================================================================
        $this->validate([
            'receipt' => 'required|image|max:2048',
        ], [
            'receipt.required' => 'آپلود تصویر فیش واریزی الزامی است.',
        ]);

        $path = $this->receipt->store('attachments/orders', 'public');

        $newPassword = null;
        $customerId = null;

        if (Auth::check()) {
            $customerId = Auth::id();
        } else {
            $customer = User::where('phone', $this->phone)->where('creator', $this->sellerId ?? 1)->first();
            if (!$customer) {
                $newPassword = rand(100000, 999999);
                $customer = User::create([
                    'name'      => $this->name,
                    'phone'     => $this->phone,
                    'email'     => $this->email,
                    'password'  => \Illuminate\Support\Facades\Hash::make($newPassword),
                    'role'      => 'customer',
                    'creator'   => $this->sellerId ?? 1,
                    'is_active' => 1,
                ]);
            }
            $customerId = $customer->id;
        }

        StoreOrder::create([
            'agent_id'      => $this->sellerId,
            'user_id'       => $customerId,
            'group_id'      => $this->selectedPlan->id,
            'phone'         => $this->phone,
            'email'         => $this->email,
            'price'         => $securePrice,
            'receipt_image' => $path,
            'status'        => 'pending',
        ]);

        if ($newPassword) {
            session()->flash('new_account', true);
            session()->flash('username', $this->phone);
            session()->flash('password', $newPassword);
            session()->flash('success_order', 'سفارش ثبت شد! یک حساب کاربری برای پیگیری ایجاد گردید.');
        } else {
            session()->flash('success_order', 'فیش واریزی ثبت شد و در انتظار تایید مدیریت است.');
        }

        $this->resetWizard();
    }

    public function resetWizard()
    {
        $this->reset(['step', 'selectedPlan', 'selectedPlanPrice', 'receipt']);
        if(!Auth::check()) {
            $this->reset(['name', 'phone', 'email']);
        }
    }

    public function render()
    {
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

        // 👈 فیلتر بهبودیافته وایرگارد (پشتیبانی از نام‌های مختلف)
        if ($this->selectedProtocol === 'wireguard') {$plans = $allPlans->filter(function($p) {
            $name = strtolower($p->name);
            return str_contains($name, 'wireguard') || str_contains($name, 'وایرگارد') || str_contains($name, 'wg');
            });
        } elseif ($this->selectedProtocol === 'l2tp_openvpn') {$plans = $allPlans->filter(function($p) {
            $name = strtolower($p->name);
            return !str_contains($name, 'wireguard') && !str_contains($name, 'وایرگارد') && !str_contains($name, 'wg');
        });
        } else {
            $plans =$allPlans;
        }

        return view('livewire.store.storefront', [
            'plans' => $plans
        ])->layout('layouts.store')->title($this->storeData['brand_name']);
    }
}

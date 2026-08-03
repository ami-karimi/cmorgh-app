<?php
namespace App\Livewire\Store;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class PackageList extends Component
{
    public $activeTab = 'volume'; // تب پیش‌فرض: حجمی

    // متغیرهای مربوط به مودال ساخت اکانت دلخواه
    public $showModal = false;
    public $selectedPackage = null;
    public $customUsername = '';
    public $customPassword = '';

    protected $rules = [
        'customUsername' => 'required|alpha_dash|min:4|max:20|unique:vpn_accounts,username',
        'customPassword' => 'required|min:6|max:20',
    ];

    protected $messages = [
        'customUsername.required' => 'نام کاربری الزامی است.',
        'customUsername.alpha_dash' => 'نام کاربری فقط می‌تواند شامل حروف انگلیسی، اعداد، خط تیره و زیرخط باشد.',
        'customUsername.unique' => 'این نام کاربری قبلاً توسط شخص دیگری انتخاب شده است.',
        'customUsername.min' => 'نام کاربری حداقل باید ۴ کاراکتر باشد.',
        'customPassword.required' => 'رمز عبور الزامی است.',
        'customPassword.min' => 'رمز عبور حداقل باید ۶ کاراکتر باشد.',
    ];


    public function getPackagesProperty()
    {
        return collect([
            // ==========================================
            // پلن‌های حجمی (دارای قابلیت تنظیم یوزرنیم اختصاصی)
            // ==========================================
            [
                'id' => 1,
                'type' => 'volume',
                'protocol' => 'Wireguard',
                'name' => 'برنزی حجمی',
                'volume' => 20,
                'duration' => 30, // مدت زمان به روز
                'price' => 45000,
                'badge' => 'اقتصادی',
            ],
            [
                'id' => 2,
                'type' => 'volume',
                'protocol' => 'Wireguard',

                'name' => 'نقره‌ای حجمی',
                'volume' => 50,
                'duration' => 30,
                'price' => 85000,
                'badge' => 'محبوب‌ترین',
            ],
            [
                'id' => 3,
                'type' => 'volume',
                'protocol' => 'Wireguard',

                'name' => 'طلایی حجمی',
                'volume' => 100,
                'duration' => 60,
                'price' => 150000,
                'badge' => 'بصرفه',
            ],
            [
                'id' => 4,
                'type' => 'volume',
                'protocol' => 'Wireguard',

                'name' => 'الماس حجمی',
                'volume' => 200,
                'duration' => 90,
                'price' => 280000,
                'badge' => 'ویژه گیمرها',
            ],

            // ==========================================
            // پلن‌های نامحدود (تک‌کاربره یا دوکاربره)
            // ==========================================
            [
                'id' => 5,
                'type' => 'unlimited',
                'protocol' => 'Wireguard',

                'name' => 'یک ماهه نامحدود',
                'volume' => 'نامحدود',
                'duration' => 30,
                'price' => 140000,
                'badge' => null,
            ],
            [
                'id' => 6,
                'type' => 'unlimited',
                'protocol' => 'Wireguard',

                'name' => 'دو ماهه نامحدود',
                'volume' => 'نامحدود',
                'duration' => 60,
                'price' => 260000,
                'badge' => 'پیشنهاد ویژه',
            ],
            [
                'id' => 7,
                'type' => 'unlimited',
                'protocol' => 'Wireguard',

                'name' => 'سه ماهه نامحدود',
                'volume' => 'نامحدود',
                'duration' => 90,
                'price' => 380000,
                'badge' => 'VIP',
            ],
        ]);
    }

    // تغییر تب بین حجمی و نامحدود
    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    // باز کردن مودال برای پلن‌های L2TP/OpenVPN
    public function openBuyModal($packageId)
    {
        $this->reset(['customUsername', 'customPassword']);
        $this->resetValidation();

        $this->selectedPackage = $this->packages->firstWhere('id', $packageId);

        // اگر کاربر لاگین نکرده باشد، ابتدا به صفحه ورود هدایت شود
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // اگر پلن حجمی و از نوع L2TP/OpenVPN بود، مودال ساخت اکانت باز شود
        if ($this->selectedPackage->protocol === 'l2tp_openvpn') {
            $this->showModal = true;
        } else {
            // برای وایرگارد نیازی به یوزرنیم سفارشی نیست و سیستم کلید می‌سازد مستقیم به درگاه می‌رود
            $this->proceedToPayment($this->selectedPackage->id);
        }
    }

    // تایید فرم و انتقال به درگاه پرداخت
    public function confirmAndPay()
    {
        $this->validate();

        // در اینجا کاربر به درگاه پرداخت متصل می‌شود
        // یوزرنیم و پسورد دلخواه او را در Session یا جدول Orders موقت ذخیره می‌کنیم
        session()->put('pending_vpn_account', [
            'package_id' => $this->selectedPackage->id,
            'username' => $this->customUsername,
            'password' => $this->customPassword
        ]);

        // هدایت به مسیر پرداخت (که در مراحل قبل ساختیم)
        // return redirect()->route('payment.start');

        // جهت تست فعلی پیام نمایش می‌دهیم:
        session()->flash('success', "اکانت {$this->customUsername} آماده اتصال به درگاه پرداخت است!");
        $this->showModal = false;
    }

    public function render()
    {

        $packages = $this->packages->where('type', $this->activeTab)->values();

        return view('livewire.store.package-list', [
            'packages' => (object) $packages
        ]);
    }
}

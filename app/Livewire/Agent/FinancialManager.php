<?php

namespace App\Livewire\Agent;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Financial;
use App\Models\User;
use App\Models\AgentBankAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\TelegramNotificationService;


#[Title('مدیریت مالی | پنل نمایندگی')]
#[Layout('layouts.agent')]
class FinancialManager extends Component
{
    use WithFileUploads, WithPagination;

    public $activeTab = 'my_wallet';

    // متغیرهای فرم آپلود فیش نماینده
    public $myAmount, $myDescription, $myReceipt;
    public $myTransactionSearch = '';

    // متغیرهای فرم شارژ/کسر مستقیم زیرنماینده
    public $subAgentId, $subAmount, $subType = 'plus', $subDescription;
    public $subTransactionSearch = '';

    // متغیرهای حساب بانکی
    public $bankAccount;

    // متغیرهای مودال بررسی فیش
    public $isReviewModalOpen = false;
    public $reviewTransactionId;
    public $reviewTransaction;
    public $reviewSubAgentName;
    public $showBankDetails = false; // وضعیت نمایش اطلاعات حساس


    public function mount()
    {
        $this->loadBankAccount();
    }

    public function toggleBankDetails()
    {
        $this->showBankDetails = !$this->showBankDetails;
    }

    public function maskCardNumber($cardNumber)
    {
        $cardNumber = preg_replace('/\s+/', '', $cardNumber);
        $length = strlen($cardNumber);
        if ($length < 8) {
            return str_repeat('*', $length);
        }
        $lastFour = substr($cardNumber, -4);
        $masked = str_repeat('*', $length - 4) . $lastFour;

        // فرمت کردن با خط تیره (مانند کارت بانکی)
        return implode('-', str_split($masked, 4));
    }

    public function maskShebaNumber($shebaNumber)
    {
        $shebaNumber = preg_replace('/\s+/', '', $shebaNumber);
        $length = strlen($shebaNumber);
        if ($length < 8) {
            return str_repeat('*', $length);
        }
        $lastFour = substr($shebaNumber, -4);
        $masked = str_repeat('*', $length - 4) . $lastFour;

        // فرمت کردن با خط تیره هر ۴ رقم
        return implode('-', str_split($masked, 4));
    }


    // ==========================================
    // 📋 بارگذاری اطلاعات حساب بانکی مدیر اصلی
    // ==========================================
    private function loadBankAccount()
    {
        $account = AgentBankAccount::whereHas('user', function($query) {
            $query->where('role', 'manager');
        })
            ->where('is_show', 1)
            ->orderBy('id', 'asc')
            ->first();

        $this->bankAccount = $account;
    }

    // ==========================================
    // 🔴 ۱. ثبت فیش واریزی توسط خود نماینده
    // ==========================================
    public function submitMyReceipt()
    {
        $this->validate([
            'myAmount' => 'required|numeric|min:1000',
            'myReceipt' => 'required|image|max:2048',
            'myDescription' => 'nullable|string|max:255',
        ]);

        $agent = Auth::user();
        $path = $this->myReceipt->store('attachments/payments', 'public');

        $receipt =  Financial::create([
            'creator' => $agent->id,
            'for' => $agent->id,
            'type' => 'plus',
            'price' => $this->myAmount,
            'description' => $this->myDescription ?? 'ثبت فیش واریزی',
            'attachment' => $path,
            'approved' => 0,
        ]);

        try {
            $notificationService = new TelegramNotificationService();
            $notificationService->notifyNewReceipt($receipt);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('خطا در ارسال نوتیف تلگرام: ' . $e->getMessage());
        }

        $this->reset(['myAmount', 'myReceipt', 'myDescription']);
        $this->dispatch('toast', [
            'type' => 'success',
            'title' => 'فیش ثبت شد',
            'message' => 'فیش شما با موفقیت ثبت شد و منتظر تایید مدیریت است.'
        ]);
    }

    // ==========================================
    // 🔵 ۲. شارژ یا کسر مستقیم زیرنماینده
    // ==========================================
    public function manageSubAgentBalance()
    {
        $this->validate([
            'subAgentId' => 'required|exists:users,id',
            'subAmount' => 'required|numeric|min:1000',
            'subType' => 'required|in:plus,minus',
            'subDescription' => 'nullable|string|max:255',
        ]);

        $agent = Auth::user();

        // بررسی اینکه زیرنماینده واقعاً زیرمجموعه این نماینده باشد
        $subAgent = User::where('creator', $agent->id)
            ->where('role', 'sub_agent')
            ->find($this->subAgentId);

        if (!$subAgent) {
            $this->addError('subAgentId', 'زیرنماینده انتخاب شده معتبر نیست.');
            return;
        }

        // بررسی موجودی کافی
        if ($this->subType === 'plus' && $agent->balance < $this->subAmount) {
            $this->addError('subAmount', 'موجودی کیف پول شما برای این شارژ کافی نیست!');
            return;
        }

        if ($this->subType === 'minus' && $subAgent->balance < $this->subAmount) {
            $this->addError('subAmount', 'موجودی کیف پول زیرنماینده برای این کسر کافی نیست!');
            return;
        }

        DB::transaction(function () use ($agent, $subAgent) {
            // ۱. ثبت تراکنش برای زیرنماینده
            Financial::create([
                'creator' => $agent->id,
                'for' => $this->subAgentId,
                'type' => $this->subType,
                'price' => $this->subAmount,
                'description' => $this->subDescription ?? 'شارژ مستقیم توسط نماینده ارشد',
                'approved' => 1,
            ]);

            // ۲. تراکنش برای نماینده
            $agentTransactionType = $this->subType === 'plus' ? 'minus' : 'plus';
            $agentDesc = $this->subType === 'plus'
                ? "انتقال شارژ به زیرنماینده ({$subAgent->name})"
                : "کسر از زیرنماینده و بازگشت به کیف پول ({$subAgent->name})";

            Financial::create([
                'creator' => $agent->id,
                'for' => $agent->id,
                'type' => $agentTransactionType,
                'price' => $this->subAmount,
                'description' => $agentDesc,
                'approved' => 1,
            ]);
        });

        $this->reset(['subAgentId', 'subAmount', 'subDescription', 'subType']);
        $this->dispatch('toast', [
            'type' => 'success',
            'title' => 'عملیات موفق',
            'message' => 'عملیات مالی روی زیرنماینده با موفقیت انجام شد.'
        ]);
    }

    // ==========================================
    // 🟢 ۳. باز کردن مودال بررسی فیش
    // ==========================================
    public function openReviewModal($transactionId)
    {
        $transaction = Financial::findOrFail($transactionId);
        $agent = Auth::user();

        // بررسی امنیتی
        $isMySubAgent = User::where('creator', $agent->id)
            ->where('id', $transaction->for)
            ->exists();

        if (!$isMySubAgent) {
            $this->dispatch('toast', [
                'type' => 'error',
                'title' => 'خطا',
                'message' => 'شما مجاز به بررسی این فیش نیستید.'
            ]);
            return;
        }

        $this->reviewTransactionId = $transactionId;
        $this->reviewTransaction = $transaction;
        $this->reviewSubAgentName = User::find($transaction->for)->name ?? 'کاربر نامشخص';
        $this->isReviewModalOpen = true;
    }

    // ==========================================
    // 🟢 ۴. تایید یا رد فیش زیرنماینده
    // ==========================================
    public function toggleSubAgentReceipt($transactionId, $status)
    {
        $transaction = Financial::findOrFail($transactionId);
        $agent = Auth::user();

        // بررسی امنیتی
        $isMySubAgent = User::where('creator', $agent->id)
            ->where('id', $transaction->for)
            ->exists();

        if (!$isMySubAgent) {
            $this->dispatch('toast', [
                'type' => 'error',
                'title' => 'خطا',
                'message' => 'شما مجاز به تایید این فیش نیستید.'
            ]);
            $this->isReviewModalOpen = false;
            return;
        }

        // رد کردن (status = 2)
        if ($status == 2) {
            $transaction->update(['approved' => 2]);
            $this->isReviewModalOpen = false;
            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'فیش رد شد',
                'message' => 'درخواست فیش با موفقیت رد شد.'
            ]);
            return;
        }

        // تایید (status = 1)
        if ($status == 1 && $transaction->approved == 0) {
            if ($agent->balance < $transaction->price) {
                $this->dispatch('toast', [
                    'type' => 'error',
                    'title' => 'موجودی کافی نیست',
                    'message' => 'موجودی شما برای تایید این فیش کافی نیست.'
                ]);
                return;
            }

            DB::transaction(function () use ($agent, $transaction) {
                $transaction->update(['approved' => 1]);

                Financial::create([
                    'creator' => $agent->id,
                    'for' => $agent->id,
                    'type' => 'minus',
                    'price' => $transaction->price,
                    'description' => "تایید فیش و کسر مبلغ برای زیرنماینده (#{$transaction->for})",
                    'approved' => 1,
                ]);
            });

            $this->isReviewModalOpen = false;
            $this->dispatch('toast', [
                'type' => 'success',
                'title' => 'فیش تایید شد',
                'message' => 'فیش تایید شد و مبلغ از کیف پول شما کسر و به زیرنماینده منتقل گردید.'
            ]);
        }
    }

    // ==========================================
    // 📝 تبدیل عدد به حروف فارسی (برای نمایش)
    // ==========================================
    public function convertToPersianWords($number)
    {
        if (!$number || $number == 0) return 'صفر';

        $number = (int)$number;
        $negative = $number < 0;
        if ($negative) $number = abs($number);

        $units = ['', 'هزار', 'میلیون', 'میلیارد', 'تریلیون'];
        $words = [
            0 => 'صفر', 1 => 'یک', 2 => 'دو', 3 => 'سه', 4 => 'چهار',
            5 => 'پنج', 6 => 'شش', 7 => 'هفت', 8 => 'هشت', 9 => 'نه',
            10 => 'ده', 11 => 'یازده', 12 => 'دوازده', 13 => 'سیزده',
            14 => 'چهارده', 15 => 'پانزده', 16 => 'شانزده', 17 => 'هفده',
            18 => 'هجده', 19 => 'نوزده', 20 => 'بیست', 30 => 'سی',
            40 => 'چهل', 50 => 'پنجاه', 60 => 'شصت', 70 => 'هفتاد',
            80 => 'هشتاد', 90 => 'نود', 100 => 'صد', 200 => 'دویست',
            300 => 'سیصد', 400 => 'چهارصد', 500 => 'پانصد',
            600 => 'ششصد', 700 => 'هفتصد', 800 => 'هشتصد', 900 => 'نهصد'
        ];

        // تابع کمکی برای تبدیل اعداد کمتر از ۱۰۰۰
        $convertLessThanThousand = function($num) use ($words) {
            if ($num == 0) return '';
            if (isset($words[$num])) return $words[$num];

            $result = '';
            if ($num >= 100) {
                $hundreds = floor($num / 100) * 100;
                $result .= $words[$hundreds];
                $num %= 100;
                if ($num > 0) $result .= ' و ';
            }
            if ($num >= 20) {
                $tens = floor($num / 10) * 10;
                $result .= $words[$tens];
                $num %= 10;
                if ($num > 0) $result .= ' و ' . $words[$num];
            } elseif ($num > 0) {
                $result .= $words[$num];
            }
            return $result;
        };

        // تقسیم عدد به گروه‌های سه‌رقمی
        $groups = [];
        while ($number > 0) {
            $groups[] = $number % 1000;
            $number = floor($number / 1000);
        }

        $resultParts = [];
        foreach ($groups as $index => $group) {
            if ($group == 0) continue;
            $groupText = $convertLessThanThousand($group);
            if ($index > 0 && $groupText) {
                $groupText .= ' ' . $units[$index];
            }
            $resultParts[] = $groupText;
        }

        $result = implode(' و ', array_reverse($resultParts));
        return ($negative ? 'منفی ' : '') . $result;
    }

    // ==========================================
    // 📊 RENDER
    // ==========================================
    public function render()
    {
        $agent = Auth::user();

        // تراکنش‌های خود نماینده
        $myQuery = Financial::where('for', $agent->id);
        if (!empty($this->myTransactionSearch)) {
            $myQuery->where('description', 'like', '%' . $this->myTransactionSearch . '%');
        }
        $myTransactions = $myQuery->latest()->paginate(10);

        // استخراج زیرنمایندگان
        $subAgents = User::where('creator', $agent->id)
            ->where('role', 'sub_agent')
            ->get();

        $subAgentIds = $subAgents->pluck('id')->toArray();

        // تراکنش‌های زیرنمایندگان
        $subQuery = Financial::whereIn('for', $subAgentIds);
        if (!empty($this->subTransactionSearch)) {
            $subQuery->where('description', 'like', '%' . $this->subTransactionSearch . '%');
        }
        $subTransactions = $subQuery->latest()->paginate(10);

        return view('livewire.agent.financial-manager', [
            'myTransactions' => $myTransactions,
            'subAgents' => $subAgents,
            'subTransactions' => $subTransactions,
            'balance' => $agent->balance,
        ]);
    }
}

<?php
namespace App\Livewire\Auth;

use App\Utility\Functions;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\SmsService;
use Illuminate\Database\Eloquent\Casts\Attribute;

#[Title('ورود یا ثبت‌نام | همراه سیمرغ')]
#[Layout('layouts.app')]
class Login extends Component
{
    public $step = 'check_identifier';

    public $identifier = '';
    public $loginType = 'phone';

    public $password = '';
    public $name = '';
    public $email = '';
    public $otp = '';
    public $remember = false;

    // متد اول: بررسی هوشمند ورودی (ایمیل یا شماره)
    public function checkIdentifier()
    {
        $this->validate([
            'identifier' => 'required'
        ], [
            'identifier.required' => 'شماره موبایل یا ایمیل خود را وارد کنید.'
        ]);

        // تشخیص ایمیل یا شماره تماس
        if (filter_var($this->identifier, FILTER_VALIDATE_EMAIL)) {$this->loginType = 'email';
            $userExists = User::where('email',$this->identifier)->exists();
        } else {
            $this->loginType = 'phone';

            if (!preg_match('/^(0|\+98|0098)?9\d{9}$/', $this->identifier)) {$this->addError('identifier', 'فرمت ایمیل یا شماره موبایل صحیح نیست.');
                return;
            }

            $formattedPhone = $this->normalizePhoneNumber($this->identifier);
            $userExists = User::where('phone',$formattedPhone)->exists();
        }

        if ($userExists) {
            $this->step = 'login_password';
        } else {
            if ($this->loginType === 'email') {
                $this->addError('identifier', 'حساب کاربری با این ایمیل یافت نشد. برای ثبت‌نام از شماره موبایل استفاده کنید.');
                return;
            }
            $this->requestLoginOtp();
            $this->step = 'register';
        }
    }

    public function loginWithPassword()
    {
        $this->validate(['password' => 'required'], ['password.required' => 'رمز عبور را وارد کنید.']);

        $credentials = [];
        if ($this->loginType === 'email') {$credentials = ['email' => $this->identifier, 'password' =>$this->password];
        } else {
            $formattedPhone =$this->normalizePhoneNumber($this->identifier);$credentials = ['phone' => $formattedPhone, 'password' =>$this->password];
        }

        if (Auth::attempt($credentials,$this->remember)) {
            session()->regenerate();
            return $this->redirect(Auth::user()->dashboard_url, navigate: true);
        }

        $this->addError('password', 'ایمیل/شماره یا رمز عبور اشتباه است.');
    }

    public function requestLoginOtp()
    {
        if ($this->loginType === 'email') return;

        $otpCode = Functions::otp(5);
        session()->put('login_otp_' . $this->identifier,$otpCode);

        $smsService = new SmsService();
        $result =$smsService->sendPattern(
            $this->normalizePhoneNumber($this->identifier),
            env('IPPANEL_VERIFY_PATTERN'),
            ['code' => $otpCode]
        );

        if (!$result['success']) {
            $this->addError('otp',$result['message']);
            return;
        }

        $this->step = 'login_otp';
    }

    public function verifyLoginOtp()
    {
        $this->validate(['otp' => 'required|numeric']);
        $savedOtp = session()->get('login_otp_' .$this->identifier);

        if ($savedOtp && $this->otp ===$savedOtp) {
            session()->forget('login_otp_' . $this->identifier);$formattedPhone = $this->normalizePhoneNumber($this->identifier);
            $user = User::where('phone',$formattedPhone)->first();

            Auth::login($user,$this->remember);
            session()->regenerate();
            return $this->redirect(Auth::user()->dashboard_url, navigate: true);
        }

        $this->addError('otp', 'کد تایید اشتباه است.');
    }
    public function NavigateLink(){

    }

    public function submitForm()
    {
        if ($this->step === 'check_identifier') {
            $this->checkIdentifier();
        } elseif ($this->step === 'login_password') {
            $this->loginWithPassword();
        } elseif ($this->step === 'login_otp') {
            $this->verifyLoginOtp();
        } elseif ($this->step === 'register') {
            $this->registerNewUser();
        }
    }

    public function registerNewUser()
    {
        $this->validate([
            'name' => 'required|min:3',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'required|min:6',
            'otp' => 'required|numeric'
        ], [
            'name.required' => 'نام خود را وارد کنید.',
            'email.email' => 'فرمت ایمیل نامعتبر است.',
            'email.unique' => 'این ایمیل قبلاً در سیستم ثبت شده است.',
            'password.min' => 'رمز عبور باید حداقل ۶ کاراکتر باشد.',
            'otp.required' => 'کد پیامک شده را وارد کنید.'
        ]);

        $savedOtp = session()->get('login_otp_' . $this->identifier);

        if (!$savedOtp || $this->otp !== $savedOtp) {
            $this->addError('otp', 'کد تایید اشتباه است.');
            return;
        }

        $formattedPhone = $this->normalizePhoneNumber($this->identifier);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email ?: null,
            'phone' => $formattedPhone,
            'password' => Hash::make($this->password),
        ]);

        session()->forget('login_otp_' . $this->identifier);
        Auth::login($user);
        session()->regenerate();
        return $this->redirect(Auth::user()->dashboard_url, navigate: true);
    }

    public function resetIdentifier()
    {
        $this->reset(['step', 'password', 'otp', 'name', 'email', 'loginType']);
        $this->step = 'check_identifier';
    }

    private function normalizePhoneNumber($number)
    {
        if (empty($number)) return$number;
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic  = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];$english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $number  = str_replace($persian, $english,$number);
        $number  = str_replace($arabic, $english,$number);
        $number = preg_replace('/[^0-9\+]/', '',$number);

        if (preg_match('/^09[0-9]{9}$/', $number)) return '+98' . substr($number, 1);
        if (preg_match('/^0098(9[0-9]{9})$/',$number, $matches)) return '+98' .$matches[1];
        if (preg_match('/^9[0-9]{9}$/', $number)) return '+98' .$number;

        return $number;
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}

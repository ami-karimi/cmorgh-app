<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Enums\Role;
use Morilog\Jalali\Jalalian;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_MANAGER = 'manager';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_AGENT = 'agent';
    public const ROLE_SUB_AGENT = 'sub_agent';
    public const ROLE_CUSTOMER = 'customer';


    public static function getAvailableRoles(): array
    {
        return [
            self::ROLE_MANAGER => 'مدیر ارشد',
            self::ROLE_ADMIN => 'مدیر سیستم',
            self::ROLE_AGENT => 'نماینده فروش',
            self::ROLE_SUB_AGENT => 'زیر نماینده',
            self::ROLE_CUSTOMER => 'مشتری',
        ];
    }

    public function getRoleLabelAttribute()
    {
        return self::getAvailableRoles()[$this->role] ?? 'نامشخص';
    }
    public function getRoleCssAttribute(): string
    {
        return match($this->role) {
            self::ROLE_MANAGER   => 'bg-fuchsia-500/10 text-fuchsia-400 border-fuchsia-500/20',
            self::ROLE_ADMIN     => 'bg-red-500/10 text-red-400 border-red-500/20',
            self::ROLE_AGENT     => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
            self::ROLE_SUB_AGENT => 'bg-cyan-500/10 text-cyan-400 border-cyan-500/20',
            self::ROLE_CUSTOMER  => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
            default              => 'bg-zinc-500/10 text-zinc-400 border-zinc-500/20',
        };
    }


    public function getCreatedAtShamsiAttribute()
    {
        if (!$this->created_at) {
            return '---';
        }

        return Jalalian::fromCarbon($this->created_at)->format('Y/m/d H:i');
    }

    public function parentAgent()
    {
        return $this->belongsTo(User::class, 'creator');
    }

    public function getGroupPrice($group)
    {
        $groupId = $group instanceof Group ? $group->id : $group;
        $defaultPrice = $group instanceof Group ? $group->price : (Group::find($groupId)?->price ?? 0);

        $sellerId = $this->parentAgent->id;

        if (!$sellerId) {
            return $defaultPrice;
        }

        $customPrice = DB::table('agent_plan_prices')
            ->where('agent_id', $sellerId)
            ->where('group_id', $groupId)
            ->value('selling_price');

        return !is_null($customPrice) ? $customPrice : $defaultPrice;
    }

    public function toShamsi($column = 'created_at', $format = 'Y/m/d H:i')
    {
        if (empty($this->{$column})) {
            return '---';
        }

        return Jalalian::fromCarbon($this->{$column})->format($format);
    }

    public function sessions()
    {
        return DB::table('sessions')->where('user_id', $this->id);
    }

    public function getLastLoginAttribute()
    {
        $session = $this->sessions()->latest('last_activity')->first();

        return $session ? \Carbon\Carbon::createFromTimestamp($session->last_activity) : null;
    }

    public function getLastLoginForHumansAttribute()
    {
        $lastLogin = $this->getLastLoginAttribute();

        return $lastLogin ? $lastLogin->diffForHumans() : 'هرگز آنلاین نبوده';
    }

    public function vpnAccounts() {
        return $this->belongsToMany(Accounts::class, 'user_accounts', 'user_id', 'account_id')->withTimestamps();
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
     protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    protected function phone(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => $this->normalizePhoneNumber($value),
        );
    }


    public function getCalculatedPrice($basePrice)
    {
        $discount = $this->discount_percent ?? 0;

        if ($discount > 0) {
            $discounted = $basePrice - ($basePrice * $discount / 100);
            return max(0, round($discounted)); // رند کردن قیمت نهایی
        }

        return $basePrice;
    }

    public function store()
    {
        return $this->hasOne(\App\Models\AgentStore::class, 'user_id');
    }

    // رابطه با جدول شماره حساب‌های بانکی نماینده
    public function bankAccounts()
    {
        return $this->hasMany(\App\Models\AgentBankAccount::class, 'user_id');
    }

    private function normalizePhoneNumber($number)
    {
        if (empty($number)) return $number;

        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic  = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        $number  = str_replace($persian, $english, $number);
        $number  = str_replace($arabic, $english, $number);

        $number = preg_replace('/[^0-9\+]/', '', $number);

        // ۳. الگویابی و تبدیل به فرمت +98
        if (preg_match('/^09[0-9]{9}$/', $number)) {
            return '+98' . substr($number, 1);

        } elseif (preg_match('/^0098(9[0-9]{9})$/', $number, $matches)) {
            return '+98' . $matches[1];

        } elseif (preg_match('/^9[0-9]{9}$/', $number)) {
            return '+98' . $number;
        }

        return $number;
    }


    protected function dashboardUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->role === 'manager' || $this->role === 'admin') {
                    return route('admin.dashboard');
                }

                if ($this->role === 'agent' || $this->role === 'sub_agent') {
                    return route('reseller.dashboard');
                }

                return route('customer.dashboard');
            },
        );
    }


    public function hasRole(Role $role): bool
    {
        return $this->role === $role;
    }

    public function financials()
    {
        return $this->hasMany(\App\Models\Financial::class, 'for', 'id');
    }

    public function getBalanceAttribute()
    {
        $totalPlus = $this->financials()
            ->where('approved', 1)
            ->whereIn('type', ['plus'])
            ->sum('price');

        $totalMinus = $this->financials()
            ->where('approved', 1)
            ->whereIn('type', ['minus'])
            ->sum('price');

        return (int) ($totalPlus - $totalMinus);
    }
    public function getDebtBalanceAttribute()
    {
        $totalPlus = $this->financials()
            ->where('approved', 1)
            ->whereIn('type', ['plus_amn'])
            ->sum('price');

        $totalMinus = $this->financials()
            ->where('approved', 1)
            ->whereIn('type', ['minus_amn'])
            ->sum('price');

        return (int) ($totalPlus - $totalMinus);
    }

    public function getFormattedBalanceAttribute()
    {
        return number_format($this->balance) . ' تومان';
    }


    public function accounts()
    {
        return $this->hasMany(Accounts::class, 'creator');
    }

}

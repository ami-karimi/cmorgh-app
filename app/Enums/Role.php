<?php
namespace App\Enums;

enum Role: string
{
    case Manager = 'manager';
    case Admin = 'admin';
    case Agent = 'agent';
    case SubAgent = 'sub_agent';
    case Customer = 'customer';

    public function label(): string
    {
        return match($this) {
            self::Manager => 'مدیر ارشد',
            self::Admin => 'مدیر سیستم',
            self::Agent => 'نماینده فروش',
            self::SubAgent => 'زیر نماینده',
            self::Customer => 'مشتری',
        };
    }
}

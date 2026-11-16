<?php
namespace App\Services;
use App\Models\SupportTicket;
use Illuminate\Support\Str;
class SupportService
{
    public static function createTicket(array $data): SupportTicket
    {
        return SupportTicket::create($data + ['number'=>'TKT-'.strtoupper(Str::random(10)), 'status'=>'new', 'last_reply_at'=>now()]);
    }
    public static function statuses(): array { return ['new'=>'ثبت شده','in_progress'=>'در حال بررسی','waiting_customer'=>'منتظر پاسخ مشتری','answered'=>'پاسخ داده شده','resolved'=>'حل شده','closed'=>'بسته شده']; }
}

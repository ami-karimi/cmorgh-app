<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Setting;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Storage;

#[Title('تنظیمات کلان سیستم | همراه سیمرغ')]
#[Layout('layouts.admin')]
class SiteSettings extends Component
{
    use WithFileUploads;

    public $activeTab = 'general';

    // متغیرهای فرم (منطبق با کلیدهای دیتابیس)
    public $site_title;
    public $qr_watermark;
    public $telegram_support;
    public $rules_text;

    // دسترسی‌ها
    public $create_wg_account;
    public $create_op_account;
    public $create_v2_account;
    public $maintenance_mode;

    // متغیرهای مربوط به فایل
    public $logo;
    public $current_logo;

    public function mount()
    {
        $this->site_title = Setting::get('SITE_TITLE', 'همراه سیمرغ ایران');
        $this->qr_watermark = Setting::get('QR_WATRMARK', 'cmorgh VPN');
        $this->current_logo = Setting::get('SITE_LOGO');

        $this->create_wg_account = (bool) Setting::get('CREATE_WG_ACCOUNT', 1);
        $this->create_op_account = (bool) Setting::get('CREATE_OP_ACCOUNT', 1);
        $this->create_v2_account = (bool) Setting::get('CREATE_V2_ACCOUNT', 0);

        $this->maintenance_mode = (bool) Setting::get('MAINTENANCE_MODE', 0);
        $this->telegram_support = Setting::get('TELEGRAM_SUPPORT', 'https://t.me/');
        $this->rules_text = Setting::get('RULES_TEXT', 'قوانین استفاده...');
    }

    public function save()
    {
        $this->validate([
            'site_title' => 'required|string|max:255',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($this->logo) {
            $logoPath = $this->logo->store('general', 'public');
            $logoUrl = asset('storage/' . $logoPath);
            Setting::set('SITE_LOGO', $logoUrl, 'general', 'public');
            $this->current_logo = $logoUrl;
        }

        Setting::set('SITE_TITLE', $this->site_title, 'general', 'public');
        Setting::set('QR_WATRMARK', $this->qr_watermark, 'general', 'public');

        Setting::set('CREATE_WG_ACCOUNT', $this->create_wg_account ? '1' : '0', 'general', 'public');
        Setting::set('CREATE_OP_ACCOUNT', $this->create_op_account ? '1' : '0', 'general', 'public');
        Setting::set('CREATE_V2_ACCOUNT', $this->create_v2_account ? '1' : '0', 'general', 'public');

        Setting::set('MAINTENANCE_MODE', $this->maintenance_mode ? '1' : '0', 'general', 'public');
        Setting::set('TELEGRAM_SUPPORT', $this->telegram_support, 'general', 'public');
        Setting::set('RULES_TEXT', $this->rules_text, 'general', 'public');

        session()->flash('message', 'تنظیمات سایت با موفقیت بروزرسانی شد.');
    }

    public function render()
    {
        return view('livewire.admin.site-settings');
    }
}

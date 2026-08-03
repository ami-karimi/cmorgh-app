<?php
namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads; // اضافه شدن قابلیت آپلود
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Nas;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
#[Title('مدیریت سرورها | همراه سیمرغ')]
#[Layout('layouts.admin')]
class ServerManager extends Component
{
    use WithPagination, WithFileUploads; // فعال‌سازی آپلود در کلاس

    public $serverId = null;
    public $isFormOpen = false;

    public $name, $ipaddress, $server_location, $server_location_id, $description;
    public $is_enabled = true, $in_app = true, $unlimited = false;

    // آرایه پروتکل‌ها
    public $server_type = [];

    // فایل‌های آپلودی
    public $flag_file; // برای دریافت فایل پرچم
    public $openvpn_file; // برای دریافت فایل .ovpn

    // متغیرهای L2TP و Mikrotik
    public $mikrotik_server = false; // تغییر به boolean برای کارکرد بهتر فرم داینامیک
    public $mikrotik_domain, $mikrotik_port, $mikrotik_username, $mikrotik_password;
    public $l2tp_address, $secret = '123456';

    // متغیرهای V2Ray
    public $username_v2ray, $password_v2ray, $port_v2ray, $cdn_address_v2ray;

    public $ssh_server = 0, $ssh_username, $ssh_password, $ssh_port;
    public $openvpn_profile, $config, $flag;

    public function resetForm()
    {
        $this->reset();
        $this->server_type = ['l2tp'];
        $this->secret = '123456';
        $this->is_enabled = true;
        $this->in_app = true;
        $this->mikrotik_server = false;
    }

    public function edit($id)
    {
        $server = Nas::findOrFail($id);

        $this->serverId = $server->id;
        $this->name = $server->name;
        $this->ipaddress = $server->ipaddress;
        $this->server_location = $server->server_location;
        $this->server_location_id = $server->server_location_id;
        $this->description = $server->description;
        $this->is_enabled = $server->is_enabled;
        $this->in_app = $server->in_app;

        $this->server_type = is_array($server->server_type) ? $server->server_type : explode(',', $server->server_type);

        $this->mikrotik_server = (bool) $server->mikrotik_server;
        $this->mikrotik_domain = $server->mikrotik_domain;
        // ... (سایر متغیرها را مثل قبل مقداردهی کنید) ...
        $this->l2tp_address = $server->l2tp_address;
        $this->secret = $server->secret;

        $this->cdn_address_v2ray = $server->cdn_address_v2ray;
        $this->port_v2ray = $server->port_v2ray;

        $this->openvpn_profile = $server->openvpn_profile;
        $this->flag = $server->flag;

        $this->isFormOpen = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'ipaddress' => 'required|string|max:255',
            'server_type' => 'required|array|min:1',
            'flag_file' => 'nullable|image|max:1024', // حداکثر 1 مگابایت برای عکس
            'openvpn_file' => 'nullable|file|max:2048', // برای فایل پروفایل
        ]);

// آپلود مستقیم عکس پرچم در پوشه public/flags
        if ($this->flag_file) {
            $flagName = time() . '_' . $this->flag_file->getClientOriginalName();
            $destinationPath = public_path('flags');

            // اگر پوشه وجود نداشت، خودش می‌سازد
            File::ensureDirectoryExists($destinationPath);

            // انتقال فیزیکی فایل از پوشه موقت لایووایر به پابلیک
            File::move($this->flag_file->getRealPath(), $destinationPath . '/' . $flagName);

            $this->flag = url('flags/' . $flagName);
        }

        // آپلود مستقیم فایل OpenVPN در پوشه public/openvpn_profiles
        if ($this->openvpn_file) {
            $ovpnName = time() . '_' . $this->openvpn_file->getClientOriginalName();
            $ovpnPath = public_path('openvpn_profiles');

            File::ensureDirectoryExists($ovpnPath);
            File::move($this->openvpn_file->getRealPath(), $ovpnPath . '/' . $ovpnName);

            $this->openvpn_profile = url('openvpn_profiles/' . $ovpnName);
        }

        Nas::updateOrCreate(
            ['id' => $this->serverId],
            [
                'name' => $this->name,
                'ipaddress' => $this->ipaddress,
                'server_location' => $this->server_location,
                'is_enabled' => $this->is_enabled,
                'in_app' => $this->in_app,
                'server_type' => $this->server_type,

                'flag' => $this->flag,

                // مقادیر L2TP / Mikrotik
                'mikrotik_server' => $this->mikrotik_server ? 1 : 0,
                'mikrotik_domain' => $this->mikrotik_domain,
                'mikrotik_password' => $this->mikrotik_password,
                'l2tp_address' => $this->l2tp_address,
                'secret' => $this->secret,

                // مقادیر V2Ray
                'port_v2ray' => $this->port_v2ray,
                'cdn_address_v2ray' => $this->cdn_address_v2ray,

                // مقدار OpenVPN
                'openvpn_profile' => $this->openvpn_profile,
            ]
        );

        session()->flash('message', $this->serverId ? 'تنظیمات سرور ویرایش شد.' : 'سرور جدید با موفقیت اضافه شد.');
        $this->resetForm();
    }

    public function toggleStatus($id)
    {
        $server = \App\Models\Nas::findOrFail($id);
        $server->is_enabled = !$server->is_enabled;
        $server->save();

        // اگر سروری که در حال ویرایش است را تگل کردیم، فرم هم آپدیت شود
        if ($this->serverId == $id) {
            $this->is_enabled = $server->is_enabled;
        }
    }


    public function render()
    {
        $servers = Nas::latest('id')->paginate(10);
        return view('livewire.admin.nas-manager', compact('servers'))->with(['header' => 'مدیریت سرورها و نودها']);
    }
}

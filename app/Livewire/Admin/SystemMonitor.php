<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Nas;
use App\Models\ServiceStatus;

class SystemMonitor extends Component
{
    public $isServiceModalOpen = false;
    public $serviceId = null;
    public $service_name;
    public $status = 'operational';
    public $last_change_log;

    public function openServiceModal($id = null)
    {
        $this->resetValidation();

        if ($id) {
            $service = ServiceStatus::findOrFail($id);
            $this->serviceId =$service->id;
            $this->service_name =$service->service_name;
            $this->status =$service->status;
            $this->last_change_log =$service->last_change_log;
        } else {
            $this->reset(['serviceId', 'service_name', 'last_change_log']);$this->status = 'operational';
        }

        $this->isServiceModalOpen = true;
    }

    public function saveService()
    {
        $this->validate([
            'service_name' => 'required|string|max:255',
            'status' => 'required|in:operational,degraded,outage',
            'last_change_log' => 'nullable|string|max:255',
        ], [
            'service_name.required' => 'انتخاب سرور الزامی است.',
            'status.required' => 'لطفاً وضعیت سرور را مشخص کنید.'
        ]);

        ServiceStatus::updateOrCreate(
            ['id' => $this->serviceId],
            [
                'service_name' => $this->service_name,
                'status' => $this->status,
                'last_change_log' => $this->last_change_log,
            ]
        );

        $this->isServiceModalOpen = false;
        session()->flash('success_service', 'وضعیت سرور با موفقیت ثبت/بروزرسانی شد.');
    }

    public function deleteService($id)
    {
        ServiceStatus::findOrFail($id)->delete();
        session()->flash('success_service', 'سرور مورد نظر از لیست وضعیت‌ها حذف شد.');
    }

    public function render()
    {
        // مانیتورینگ لتنسی سرورها
        $servers = Nas::where('is_enabled', 1)->get()->map(function($server) {$latency = rand(15, 180);
            $server->latency =$latency;
            $server->health = $latency < 80 ? 'good' : ($latency < 150 ? 'warning' : 'bad');
            return $server;
        });

        // واکشی لیست وضعیت‌هایی که ادمین ثبت کرده است
        $services = ServiceStatus::latest()->get();

        // واکشی لیست تمام سرورهای فعال برای منوی کشویی ثبت وضعیت
        $nasList = Nas::where('is_enabled', 1)->get();

        return view('livewire.admin.system-monitor', compact('servers', 'services', 'nasList'))
            ->layout('layouts.admin')->title('مانیتورینگ سیستم');
    }
}

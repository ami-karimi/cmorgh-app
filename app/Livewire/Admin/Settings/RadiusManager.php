<?php
// app/Livewire/Admin/Settings/RadiusManager.php

namespace App\Livewire\Admin\Settings;

use Livewire\Component;
use App\Services\System\RadiusServiceManager;
use Illuminate\Support\Facades\Log;

class RadiusManager extends Component
{
    public array $status = [];
    public bool $isLoading = false;
    public bool $showConfirmModal = false;
    public string $action = '';

    protected $listeners = ['refreshRadiusStatus' => 'loadStatus'];

    public function mount()
    {
        $this->loadStatus();
    }

    public function loadStatus()
    {
        $this->isLoading = true;
        try {
            $manager = new RadiusServiceManager();
            $this->status = $manager->getStatus();
        } catch (\Exception $e) {
            $this->status = ['status' => 'unknown', 'error' => $e->getMessage()];
            Log::error('Radius status error: ' . $e->getMessage());
        }
        $this->isLoading = false;
    }

    public function executeAction($action)
    {
        if (!in_array($action, ['start', 'stop', 'restart', 'reload'])) {
            return;
        }

        // برای stop و restart نیاز به تأیید داریم
        if (in_array($action, ['stop', 'restart'])) {
            $this->action = $action;
            $this->showConfirmModal = true;
            return;
        }

        $this->performAction($action);
    }

    public function confirmAction()
    {
        $this->performAction($this->action);
        $this->showConfirmModal = false;
        $this->action = '';
    }

    protected function performAction($action)
    {
        $this->isLoading = true;
        try {
            $manager = new RadiusServiceManager();
            $result = $manager->$action();

            if ($result['success']) {
                session()->flash('radius_message', "عملیات {$action} با موفقیت انجام شد.");
            } else {
                session()->flash('radius_error', "خطا در عملیات {$action}: " . ($result['error'] ?? 'نامشخص'));
            }
        } catch (\Exception $e) {
            session()->flash('radius_error', "خطا: " . $e->getMessage());
            Log::error("Radius action {$action} failed: " . $e->getMessage());
        }
        $this->isLoading = false;
        $this->loadStatus();
    }

    public function render()
    {
        return view('livewire.admin.settings.radius-manager');
    }
}

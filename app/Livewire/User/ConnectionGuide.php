<?php
namespace App\Livewire\User;

use Livewire\Component;
use App\Models\Tutorial;
use Livewire\Attributes\Title;

#[Title('راهنمای اتصال به سرویس‌ها')]
class ConnectionGuide extends Component
{
    // پلتفرم‌های موجود: Android, iOS, Windows, macOS
    public $activePlatform = 'Android';
    public $selectedProtocol = ''; // فیلتر اختیاری بر اساس پروتکل (مثل WireGuard یا V2Ray)

    public function setPlatform($platform)
    {
        $this->activePlatform = $platform;
        $this->selectedProtocol = '';
    }

    public function render()
    {
        $query = Tutorial::where('is_published', 1)
            ->where('platform', $this->activePlatform)
            ->latest();

        if ($this->selectedProtocol) {
            $query->where('protocol', $this->selectedProtocol);
        }

        $tutorials = $query->get();

        // دریافت لیست پروتکل‌های موجود برای این پلتفرم (جهت فیلتر سریع)
        $availableProtocols = Tutorial::where('is_published', 1)
            ->where('platform', $this->activePlatform)
            ->whereNotNull('protocol')
            ->pluck('protocol')
            ->unique();

        return view('livewire.user.connection-guide', [
            'tutorials'          => $tutorials,
            'availableProtocols' => $availableProtocols,
        ]);
    }
}

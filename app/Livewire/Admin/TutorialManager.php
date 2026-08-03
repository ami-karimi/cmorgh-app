<?php
namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Tutorial;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;
#[Title('مدیریت گروه‌ها و تعرفه‌ها | همراه سیمرغ')]
#[Layout('layouts.admin')]
class TutorialManager extends Component
{

    use WithFileUploads;

    public $tutorials = [];
    public $tutorial_id, $title, $platform, $protocol, $content;
    public $is_published = true;

    public $attachments = [];          // فایل‌های جدید
    public $existing_attachments = []; // فایل‌های ذخیره‌شده قبلی

    public $isModalOpen = false;

    protected $rules = [
        'title'         => 'required|string|max:255',
        'platform'      => 'required|string|max:50',
        'protocol'      => 'nullable|string|max:50',
        'content'       => 'required|string',
        'attachments.*' => 'nullable|file|max:20480',
    ];

    public function mount()
    {
        $this->loadTutorials();
    }

    public function loadTutorials()
    {
        $this->tutorials = Tutorial::latest()->get();
    }

    public function openModal()
    {
        $this->resetFields();
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetFields();
    }

    public function resetFields()
    {
        $this->tutorial_id = null;
        $this->title = '';
        $this->platform = 'Android';
        $this->protocol = 'WireGuard';
        $this->content = '';
        $this->is_published = true;
        $this->attachments = [];
        $this->existing_attachments = [];
    }

    public function edit($id)
    {
        $tutorial = Tutorial::findOrFail($id);
        $this->tutorial_id = $tutorial->id;
        $this->title = $tutorial->title;
        $this->platform = $tutorial->platform;
        $this->protocol = $tutorial->protocol;
        $this->content = $tutorial->content;
        $this->is_published = $tutorial->is_published;

        // به لطف $casts لاراول، $tutorial->attachments از قبل یک آرایه است!
        $this->existing_attachments = is_array($tutorial->attachments) ? $tutorial->attachments : [];
        $this->attachments = [];

        $this->isModalOpen = true;
    }

    public function save()
    {
        $this->validate();

        try {
            $final_attachments = is_array($this->existing_attachments) ? array_values($this->existing_attachments) : [];

            // آپلود فایل‌های جدید
            if (!empty($this->attachments) && is_array($this->attachments)) {
                foreach ($this->attachments as $file) {
                    if ($file) {
                        $final_attachments[] = $file->store('tutorials_attachments', 'public');
                    }
                }
            }

            // ذخیره مستقیم آرایه (لاراول خودش تبدیل به JSON می‌کند)
            Tutorial::updateOrCreate(
                ['id' => $this->tutorial_id],
                [
                    'title'        => $this->title,
                    'platform'     => $this->platform,
                    'protocol'     => $this->protocol,
                    'content'      => $this->content,
                    'attachments'  => array_values($final_attachments),
                    'is_published' => $this->is_published ? 1 : 0,
                ]
            );

            session()->flash('message', $this->tutorial_id ? 'آموزش ویرایش شد.' : 'آموزش جدید ثبت شد.');
            $this->closeModal();
            $this->loadTutorials();

        } catch (\Exception $e) {
            Log::error("Tutorial Save Error: " . $e->getMessage());
            session()->flash('error', 'خطا در ذخیره‌سازی: ' . $e->getMessage());
        }
    }

    public function deleteExistingAttachment($index)
    {
        $filePath = $this->existing_attachments[$index] ?? null;
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }

        unset($this->existing_attachments[$index]);
        $this->existing_attachments = array_values($this->existing_attachments);

        if ($this->tutorial_id) {
            Tutorial::where('id', $this->tutorial_id)->update(['attachments' => $this->existing_attachments]);
        }
        $this->loadTutorials();
    }

    public function removeNewAttachment($index)
    {
        unset($this->attachments[$index]);
        $this->attachments = array_values($this->attachments);
    }

    public function delete($id)
    {
        $tutorial = Tutorial::findOrFail($id);

        if (is_array($tutorial->attachments)) {
            foreach ($tutorial->attachments as $file) {
                if ($file && Storage::disk('public')->exists($file)) {
                    Storage::disk('public')->delete($file);
                }
            }
        }

        $tutorial->delete();
        session()->flash('message', 'آموزش و تمامی فایل‌های آن حذف شدند.');
        $this->loadTutorials();
    }

    public function togglePublish($id)
    {
        $tutorial = Tutorial::findOrFail($id);
        $tutorial->update(['is_published' => !$tutorial->is_published]);
        $this->loadTutorials();
    }

    public function render()
    {
        return view('livewire.admin.tutorial-manager');
    }
}

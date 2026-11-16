<?php
namespace App\Livewire\Admin;
use App\Models\SupportCategory;
use App\Models\SupportFaq;
use App\Models\SupportTicket;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
class SupportManager extends Component
{
    public string $tab='tickets'; public ?int $selectedTicket=null; public string $reply=''; public string $statusFilter=''; public string $faqQuestion='', $faqAnswer='', $faqCategory=''; public bool $faqModal=false;
    public bool $categoryModal=false; public ?int $editingCategoryId=null; public string $categoryName='', $categoryDescription='', $categoryIcon='', $categoryAudience='all'; public bool $categoryActive=true;
    public function boot(){abort_unless(auth()->check()&&in_array(auth()->user()->role,['admin','manager'],true),403);}
    public function openTicket($id){$this->selectedTicket=SupportTicket::with('messages.user','user','account','category')->findOrFail($id)->id;}
    public function sendReply(){ $this->validate(['reply'=>'required|string|max:20000']);$ticket=SupportTicket::findOrFail($this->selectedTicket);$ticket->messages()->create(['user_id'=>auth()->id(),'message'=>$this->reply]);$ticket->update(['status'=>'answered','last_reply_at'=>now()]);$this->reply=''; }
    public function setStatus($status){$ticket=SupportTicket::findOrFail($this->selectedTicket);$ticket->update(['status'=>$status,'closed_at'=>$status==='closed'?now():null]);}
    public function saveFaq(){ $this->validate(['faqQuestion'=>'required|string|max:500','faqAnswer'=>'required|string|max:30000','faqCategory'=>'nullable|exists:support_categories,id']);SupportFaq::create(['question'=>$this->faqQuestion,'answer'=>$this->faqAnswer,'category_id'=>$this->faqCategory?:null,'is_published'=>true]);$this->reset(['faqQuestion','faqAnswer','faqCategory','faqModal']);session()->flash('success','FAQ ذخیره شد.'); }
    public function openCategory(?int $id=null): void { $this->resetValidation(); $this->reset(['editingCategoryId','categoryName','categoryDescription','categoryIcon','categoryAudience']); $this->categoryActive=true; if($id){$category=SupportCategory::findOrFail($id);$this->editingCategoryId=$id;$this->categoryName=$category->name;$this->categoryDescription=$category->description??'';$this->categoryIcon=$category->icon??'';$this->categoryAudience=$category->audience??'all';$this->categoryActive=(bool)$category->is_active;} $this->categoryModal=true; }
    public function saveCategory(): void { $this->validate(['categoryName'=>'required|string|max:120','categoryDescription'=>'nullable|string|max:500','categoryIcon'=>'nullable|string|max:30','categoryAudience'=>'required|in:all,customers,agents']); $data=['name'=>$this->categoryName,'description'=>$this->categoryDescription,'icon'=>$this->categoryIcon,'audience'=>$this->categoryAudience,'is_active'=>$this->categoryActive]; if($this->editingCategoryId) SupportCategory::findOrFail($this->editingCategoryId)->update($data); else SupportCategory::create($data+['slug'=>\Illuminate\Support\Str::slug($this->categoryName).'-'.\Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(6))]); $this->categoryModal=false; session()->flash('success','دسته‌بندی پشتیبانی ذخیره شد.'); }
    public function toggleCategory(int $id): void { $category=SupportCategory::findOrFail($id);$category->update(['is_active'=>!$category->is_active]); }
    public function deleteCategory(int $id): void { $category=SupportCategory::findOrFail($id); if($category->tickets()->exists()||$category->faqs()->exists()){session()->flash('error','این دسته‌بندی دارای تیکت یا FAQ است و حذف نمی‌شود؛ آن را غیرفعال کنید.');return;} $category->delete();session()->flash('success','دسته‌بندی حذف شد.'); }
    public function render(){ $tickets=SupportTicket::with(['user','category','account'])->when($this->statusFilter,fn($q)=>$q->where('status',$this->statusFilter))->latest()->limit(50)->get();$selected=$this->selectedTicket?SupportTicket::with('messages.user','user','account','category')->find($this->selectedTicket):null;$categories=SupportCategory::withCount(['faqs','tickets'])->orderBy('sort_order')->orderBy('name')->get();$activeCategories=$categories->where('is_active',true);$faqs=SupportFaq::with('category')->latest()->get();return view('livewire.admin.support-manager',compact('tickets','selected','categories','activeCategories','faqs'))->layout('layouts.admin')->title('مدیریت پشتیبانی');}
}

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
    public function boot(){abort_unless(auth()->check()&&in_array(auth()->user()->role,['admin','manager'],true),403);}
    public function openTicket($id){$this->selectedTicket=SupportTicket::with('messages.user','user','account','category')->findOrFail($id)->id;}
    public function sendReply(){ $this->validate(['reply'=>'required|string|max:20000']);$ticket=SupportTicket::findOrFail($this->selectedTicket);$ticket->messages()->create(['user_id'=>auth()->id(),'message'=>$this->reply]);$ticket->update(['status'=>'answered','last_reply_at'=>now()]);$this->reply=''; }
    public function setStatus($status){$ticket=SupportTicket::findOrFail($this->selectedTicket);$ticket->update(['status'=>$status,'closed_at'=>$status==='closed'?now():null]);}
    public function saveFaq(){ $this->validate(['faqQuestion'=>'required|string|max:500','faqAnswer'=>'required|string|max:30000','faqCategory'=>'nullable|exists:support_categories,id']);SupportFaq::create(['question'=>$this->faqQuestion,'answer'=>$this->faqAnswer,'category_id'=>$this->faqCategory?:null,'is_published'=>true]);$this->reset(['faqQuestion','faqAnswer','faqCategory','faqModal']);session()->flash('success','FAQ ذخیره شد.'); }
    public function render(){ $tickets=SupportTicket::with(['user','category','account'])->when($this->statusFilter,fn($q)=>$q->where('status',$this->statusFilter))->latest()->limit(50)->get();$selected=$this->selectedTicket?SupportTicket::with('messages.user','user','account','category')->find($this->selectedTicket):null;$categories=SupportCategory::where('is_active',true)->get();$faqs=SupportFaq::with('category')->latest()->get();return view('livewire.admin.support-manager',compact('tickets','selected','categories','faqs'))->layout('layouts.admin')->title('مدیریت پشتیبانی');}
}

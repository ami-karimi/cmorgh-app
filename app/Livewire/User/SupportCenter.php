<?php
namespace App\Livewire\User;
use App\Models\Accounts;
use App\Models\SupportCategory;
use App\Models\SupportFaq;
use App\Models\SupportTicket;
use App\Services\SupportService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
class SupportCenter extends Component
{
    use WithFileUploads;
    public int $step=1; public string $categoryId=''; public string $accountId=''; public string $search=''; public string $subject=''; public string $message=''; public string $priority='normal'; public $attachment; public ?int $selectedTicket=null;
    public function chooseCategory($id): void {$this->categoryId=(string)$id;$this->step=2;}
    public function chooseAccount($id): void {$this->accountId=(string)$id;$this->step=3;}
    public function showFaq($id): void {SupportFaq::published()->whereKey($id)->increment('views');}
    public function submitTicket(): void
    {
        $this->validate(['categoryId'=>'nullable|exists:support_categories,id','accountId'=>'nullable|exists:accounts,id','subject'=>'required|string|max:255','message'=>'required|string|max:20000','priority'=>'required|in:low,normal,high','attachment'=>'nullable|file|max:10240|mimes:jpg,jpeg,png,webp,pdf,txt,zip']);
        $account=$this->accountId?Accounts::whereKey($this->accountId)->where(function($q){$q->where('creator',auth()->id())->orWhereHas('users',fn($u)=>$u->whereKey(auth()->id()));})->firstOrFail():null;
        $ticket=SupportService::createTicket(['user_id'=>auth()->id(),'category_id'=>$this->categoryId?:null,'account_id'=>$account?->id,'subject'=>$this->subject,'initial_message'=>$this->message,'priority'=>$this->priority]);
        $ticket->messages()->create(['user_id'=>auth()->id(),'message'=>$this->message]);
        if($this->attachment){$path=$this->attachment->store('support/attachments','local');$ticket->attachments()->create(['uploaded_by'=>auth()->id(),'path'=>$path,'name'=>$this->attachment->getClientOriginalName(),'mime'=>$this->attachment->getMimeType(),'size'=>$this->attachment->getSize()]);}
        $this->selectedTicket=$ticket->id;$this->step=4;$this->reset(['subject','message','attachment']);session()->flash('success','تیکت شما با موفقیت ثبت شد. کد پیگیری: '.$ticket->number);
    }
    public function reply(): void
    {
        $this->validate(['message'=>'required|string|max:20000']);$ticket=SupportTicket::visibleTo(auth()->user())->findOrFail($this->selectedTicket);abort_if($ticket->status==='closed',403);$ticket->messages()->create(['user_id'=>auth()->id(),'message'=>$this->message]);$ticket->update(['status'=>'new','last_reply_at'=>now()]);$this->message='';
    }
    public function render(){
        $categories=SupportCategory::where('is_active',true)->withCount(['faqs'=>fn($q)=>$q->where('is_published',true)])->orderBy('sort_order')->orderBy('name')->get();
        $faqs=SupportFaq::published()->with('category')->when($this->categoryId,fn($q)=>$q->where('category_id',$this->categoryId))->when($this->search,fn($q)=>$q->where(fn($x)=>$x->where('question','like','%'.$this->search.'%')->orWhere('answer','like','%'.$this->search.'%')))->orderBy('sort_order')->latest('id')->limit($this->search||$this->categoryId?30:8)->get();
        $accounts=$this->step===2?Accounts::with('group')->where(fn($q)=>$q->where('creator',auth()->id())->orWhereHas('users',fn($u)=>$u->whereKey(auth()->id())))->latest()->get():collect();
        $tickets=SupportTicket::visibleTo(auth()->user())->with('category')->latest()->limit(8)->get();$selected=$this->selectedTicket?SupportTicket::visibleTo(auth()->user())->with('messages.user','category','account')->find($this->selectedTicket):null;
        $layout=in_array(auth()->user()->role,['agent','sub_agent'],true)?'layouts.agent':'layouts.customer';return view('livewire.user.support-center',compact('accounts','categories','faqs','tickets','selected'))->layout($layout)->title('مرکز پشتیبانی');
    }
}

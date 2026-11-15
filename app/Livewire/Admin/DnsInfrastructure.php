<?php
namespace App\Livewire\Admin;
use App\Models\DnsGameDomain;
use App\Models\DnsNode;
use App\Models\DnsPlan;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
class DnsInfrastructure extends Component
{
    public string $tab='plans'; public bool $modal=false; public ?int $editingId=null;
    public array $form=[];
    public function openCreate(): void { $this->reset(['form','editingId']); $this->modal=true; }
    public function edit(int $id): void { $model=$this->model()->findOrFail($id); $this->editingId=$id; $this->form=$model->toArray(); $this->modal=true; }
    public function save(): void
    {
        $rules=match($this->tab){
            'plans'=>['form.name'=>'required|string|max:120','form.slug'=>'required|string|max:120','form.price'=>'required|integer|min:0','form.duration_days'=>'required|integer|min:1','form.max_ips'=>'required|integer|min:1|max:20','form.max_devices'=>'required|integer|min:1|max:50'],
            'nodes'=>['form.name'=>'required|string|max:120','form.host'=>'required|string|max:255','form.port'=>'required|integer|min:1|max:65535'],
            default=>['form.game_name'=>'required|string|max:120','form.domain'=>'required|string|max:255','form.record_type'=>'required|in:A,AAAA,CNAME','form.record_value'=>'nullable|string|max:255'],
        }; $this->validate($rules);
        $this->model()->updateOrCreate(['id'=>$this->editingId],$this->payload()); $this->modal=false; session()->flash('success','اطلاعات DNS با موفقیت ذخیره شد.');
    }
    public function toggle(int $id): void { $m=$this->model()->findOrFail($id); $m->update(['is_active'=>!$m->is_active]); }
    public function delete(int $id): void { $this->model()->findOrFail($id)->delete(); }
    private function model(){return match($this->tab){'plans'=>DnsPlan::query(),'nodes'=>DnsNode::query(),default=>DnsGameDomain::query()};}
    private function payload(): array { $data=$this->form; unset($data['id'],$data['created_at'],$data['updated_at']); if($this->tab==='plans'){$data['price']=(int)($data['price']??0);$data['duration_days']=(int)($data['duration_days']??30);$data['max_ips']=(int)($data['max_ips']??1);$data['max_devices']=(int)($data['max_devices']??1);} return $data; }
    public function render(){return view('livewire.admin.dns-infrastructure',['plans'=>DnsPlan::latest()->get(),'nodes'=>DnsNode::latest()->get(),'domains'=>DnsGameDomain::latest()->get()])->layout('layouts.admin')->title('زیرساخت DNS Game');}
}

<?php

namespace App\Livewire\withdraws;

use Illuminate\Support\Facades\App;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Edit extends Component
{
    #[Validate('required|numeric')]
    public $amount;

    #[Validate('required|min:4')]
    public $code;

    public $withdraw;

    public function mount($withdraw)
    {
        $this->withdraw = $withdraw;
        $this->amount = $withdraw->amount;
        $this->code = $withdraw->code;
    }
    public function render()
    {
        if(session('locale')!==null){
            App::setLocale(session('locale'));
         }
        return view('livewire.withdraws.edit');
    }

    public function save()
    {
        $this->validate();
        if($this->amount==$this->withdraw->amount){
            $this->withdraw->update($this->all());
        }else{
            $amount =$this->amount;
            $discount = $amount * 0.1;
            // الحصول على المبلغ بعد الخصم
            $finalAmount = $amount - $discount;
            // القيمة المخصومة
            $discountAmount = $amount - $finalAmount;
            $this->withdraw->finalAmount =$finalAmount;
            $this->withdraw->discountAmount =$discountAmount;
            $this->withdraw->update($this->all());
        }
        session()->flash('success', trans('Withdraw updated').': '.$this->withdraw->id);
        return $this->redirect(route('withdraws.index'), navigate: true);
    }
}

<?php

namespace App\Livewire\charges;

use App\Models\Chat;
use Illuminate\Support\Facades\App;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Telegram\Bot\Laravel\Facades\Telegram;

class Edit extends Component
{
    #[Validate('required|numeric')]
    public $amount;

    #[Validate('required|min:4')]
    public $processid;

    public $charge;

    public function mount($charge)
    {
        $this->charge = $charge;
        $this->amount = $charge->amount;
        $this->processid = $charge->processid;
    }

    public function render()
    {
        if(session('locale')!==null){
            App::setLocale(session('locale'));
         }
        return view('livewire.charges.edit');
    }


    public function delete()
    {
        $this->charge->delete();
        session()->flash('success', trans('Success delete charge').': '.$this->charge->id);
        return $this->redirect(route('charges.index'), navigate: true);
    }

    public function save()
    {
        $this->validate();
        $chat =Chat::find($this->charge->chat_id);
        if($this->amount>$this->charge->amount){
            $diff = $this->amount-$this->charge->amount;
            $chat->balance = $chat->balance+$diff;
            $updated = $chat->save();
            if($updated){
                $response = Telegram::sendMessage([
                    'chat_id' => $chat->id,
                    'text' => '🎖 عزيزي '.$chat->username.':'.PHP_EOL.' تمت إضافة '.$diff.' NSP إلى رصيدك' ,
                ]);
            }
        }elseif($this->amount<$this->charge->amount){
            $diff = $this->charge->amount-$this->amount;
            $chat->balance = $chat->balance-$diff;
            $chat->save();
            $response = Telegram::sendMessage([
                'chat_id' => $chat->id,
                'text' => '🎖 عزيزي '.$chat->username.':'.PHP_EOL.' تم خصم '.$diff.' NSP من رصيدك' ,
            ]);
        }
        $saved = $this->charge->update($this->all());
        if($saved){
            session()->flash('success', trans('Charge updated').': '.$this->charge->id);
            return $this->redirect(route('charges.index'), navigate: true);
        }else{
            session()->flash('danger', trans('Failed to update charge').': '.$this->charge->id);
            return $this->redirect(route('charges.index'), navigate: true);
        }
    }


}

<?php

namespace App\Livewire;

use App\Jobs\SendTelegramMessage;
use App\Models\Charge;
use App\Models\Chat;
use Illuminate\Support\Facades\App;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ManualCharge extends Component
{
    public $chat;
    #[Validate('required|numeric')]
    public $amount;
    #[Validate('required|numeric')]
    public $processid;


    public function mount($chat)
    {
        $this->chat = $chat;
    }

    public function render()
    {
        if(session('locale')!==null){
            App::setLocale(session('locale'));
         }
        return view('livewire.manual-charge')->layout('admin.layouts.livewire');
    }


    public function save(){
        $this->validate();
         $created = Charge::Create([
                "amount"=>$this->amount,
                "processid"=>$this->processid,
                "status"=>"complete",
                "method"=> "سيريتل كاش",
                "chat_id"=>$this->chat
            ]);
            if($created){
                $userchat = Chat::find($this->chat);
                $userchat->balance+=$this->amount;
                $saveed = $userchat->save();
                if($saveed){
                    SendTelegramMessage::dispatch($this->chat,"✅ تم شحن رصيدك في البوت بنجاح: ".PHP_EOL."".PHP_EOL."المبلغ: ".$this->amount." NSP".PHP_EOL."رقم العملية: ".$this->processid);
                session()->flash('success', trans('Success create Syraitel cash charge'));
                }else{
                session()->flash('danger', trans('falied create  Syraitel cash charge'));
                }
            return;
        }else{
            session()->flash('danger', trans('falied create  Syraitel cash charge'));
            return;
        }
    }
}

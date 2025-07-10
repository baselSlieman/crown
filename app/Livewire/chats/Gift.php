<?php

namespace App\Livewire\chats;

use App\Models\Gift as ModelsGift;
use Illuminate\Support\Facades\App;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Telegram\Bot\Laravel\Facades\Telegram;

class Gift extends Component
{
    use WithPagination,WithoutUrlPagination;
    protected $paginationTheme ="bootstrap";
    public $chat;
    #[Validate('required|numeric')]
    public $amount;

    public function mount($chat)
    {
        $this->chat = $chat;
    }

    public function render()
    {
        if(session('locale')!==null){
            App::setLocale(session('locale'));
         }
        $gifts = ModelsGift::query()
        ->where('chat_id', $this->chat->id)
        ->orderByRaw("status = 'pending' DESC, created_at DESC")
        ->paginate(10);
        $chatId = $this->chat->id;
        return view('livewire.chats.gift',compact('chatId','gifts'));
    }

    public function confirm(){
        $this->validate();
        $chat_id = $this->chat->id;
        $amount = $this->amount;

        $hasOne= ModelsGift::where('chat_id',$chat_id)->where('status', 'pending')->exists();
        if($hasOne){
            session()->flash('danger', trans('User has pending gift'));
            return;
        }
        $chars = str_shuffle('abcdefghijklmnopqrstuvwxyz123456789');
        $code = substr($chars, 0, 5);
        $isCodeExists = ModelsGift::where('code', $code)->exists();
        while($isCodeExists){
            $chars = str_shuffle('abcdefghijklmnopqrstuvwxyz123456789');
            $code = substr($chars, 0, 6);
            $isCodeExists = ModelsGift::where('code', $code)->exists();
        }
        $created = ModelsGift::Create([
            "chat_id"=>$chat_id,
            "amount"=>$amount,
            "code"=>$code
        ]);
        if($created){
            $response = Telegram::sendMessage([
                'chat_id' => $this->chat->id,
                'parse_mode'=> 'HTML',
                'text' => 'تهانينا 🎉'.PHP_EOL.''.PHP_EOL.'تم إهدائك قسيمة رصيد:'.PHP_EOL.'الكود:  <code>'.$code.'</code>'.PHP_EOL.'القيمة: '.$amount.' NSP'.PHP_EOL.''.PHP_EOL.'يمكن صرف القسيمة من واجهة 🎁 كود الهدايا'.PHP_EOL.''.PHP_EOL.'حظاً موفقاً...' ,
            ]);
            session()->flash('success', trans('Success send gift'));
            return;
        }else{
            session()->flash('danger', trans('falied send gift'));
            return;
        }
    }
}

<?php

namespace App\Livewire;

use App\Models\Gift;
use Illuminate\Support\Facades\App;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;
use Telegram\Bot\Laravel\Facades\Telegram;

class Gifts extends Component
{
    use WithPagination,WithoutUrlPagination;
    protected $paginationTheme ="bootstrap";
    public $search = '';
    #[Validate('required|numeric')]
    public $amount;
    #[Validate('required|numeric')]
    public $gnumber;

    public function render()
    {
        if(session('locale')!==null){
            App::setLocale(session('locale'));
         }
        $gifts = Gift::query()
                ->when($this->search, function ($query) {
                    return $query->where('chat_id', 'like', '%' . $this->search . '%')
                                ->orWhere('code', $this->search)
                                ->orWhereHas('chat', function ($q2)  {
                                    $q2->where('username', 'like', '%' . $this->search . '%');
                                });
                })
            ->orderByRaw("status = 'pending' DESC, created_at DESC")
            ->paginate(10);

        return view('livewire.gifts',compact('gifts'))->layout('admin.layouts.livewire');
    }


     public function confirm(){
        $this->validate();
        $amount = $this->amount;
        $gnumber = $this->gnumber;
        $codes = '';
        for ($i=0; $i < $gnumber; $i++) {
                $chars = str_shuffle('abcdefghijklmnopqrstuvwxyz123456789');
            $code = substr($chars, 0, 5);
            $isCodeExists = Gift::where('code', $code)->exists();
            while($isCodeExists){
                $chars = str_shuffle('abcdefghijklmnopqrstuvwxyz123456789');
                $code = substr($chars, 0, 6);
                $isCodeExists = Gift::where('code', $code)->exists();
            }
            $codes.='🎁 <b><code>'.$code.'</code></b>'.PHP_EOL.''.PHP_EOL.'';
            $created = Gift::Create([
                "type"=>"random",
                "amount"=>$amount,
                "code"=>$code
            ]);
        }

        if($created){
            session()->flash('success', trans('Success create gift'));
            $subscribers = [842668006,7631183476];
            foreach ($subscribers as $chatId) {
                $response = Telegram::sendMessage([
                    'chat_id' => $chatId,
                    "parse_mode"=>"HTML",
                    'text' => '💡إعلام💡:'.PHP_EOL.''.PHP_EOL.$this->gnumber.' قسائم عشوائية أضيفت للتو بقيمة: '.$this->amount.'NSP'.PHP_EOL.''.PHP_EOL.'القسائم: '.PHP_EOL.$codes ,
                ]);
            }
            return;
        }else{
            session()->flash('danger', trans('falied create  Syraitel code'));
            return;
        }
    }

}

<?php

namespace App\Livewire\chats;

use App\Models\Affiliate as ModelsAffiliate;
use Illuminate\Support\Facades\App;
use Livewire\Component;
use Livewire\WithoutUrlPagination;
use Livewire\WithPagination;


class Affiliate extends Component
{
    use WithPagination,WithoutUrlPagination;
    protected $paginationTheme ="bootstrap";
    public $chat;

    public function mount($chat)
    {
        $this->chat = $chat;
    }
    public function render()
    {
        if(session('locale')!==null){
            App::setLocale(session('locale'));
         }
        $affiliates = ModelsAffiliate::query()
        ->where('chat_id', $this->chat->id)
        ->orderByRaw("status = 'pending' DESC, created_at DESC")
        ->paginate(10);

        $totalAffAmount = ModelsAffiliate::where('chat_id', $this->chat->id)
        ->where('month_at', date('Y-m'))
        ->where('status','pending')
        ->sum('affiliate_amount');

        $totalAffCount = ModelsAffiliate::where('chat_id', $this->chat->id)
        ->where('month_at', date('Y-m'))
        ->where('status','pending')
        ->count('affiliate_amount');

        $totalAffAmount_last = ModelsAffiliate::where('chat_id', $this->chat->id)
        ->where('month_at', date('Y-m', strtotime('last month')))
        ->where('status','pending')
        ->sum('affiliate_amount');

        $totalAffCount_last = ModelsAffiliate::where('chat_id', $this->chat->id)
        ->where('month_at', date('Y-m', strtotime('last month')))
        ->where('status','pending')
        ->count('affiliate_amount');
        return view("livewire.chats.affiliate",compact("affiliates","totalAffAmount","totalAffCount","totalAffAmount_last","totalAffCount_last"));
    }
}

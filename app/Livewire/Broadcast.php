<?php

namespace App\Livewire;

use App\Jobs\SendTelegramMessage;
use App\Livewire\chats\Message;
use App\Models\Chat;
use Illuminate\Support\Facades\App;
use Livewire\Component;

class Broadcast extends Component
{
    public $message;
    public function render()
    {
        if(session('locale')!==null){
            App::setLocale(session('locale'));
         }
        return view('livewire.broadcast')->layout('admin.layouts.livewire');
    }

    public function sendbroadcast(){
        $chunkSize = 100;
        $delaySeconds = 0;
        $message=$this->message;
        Chat::chunk($chunkSize, function($users) use ($message, &$delaySeconds) {
            foreach ($users as $user) {
                SendTelegramMessage::dispatch($user->id, $message)->delay(now()->addSeconds($delaySeconds));
            }
            $delaySeconds += 20; // زيادة التأخير 10 ثواني لكل chunk
        });
        session()->flash('success', trans('Message sent to users'));
    }
}

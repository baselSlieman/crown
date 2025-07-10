<?php

namespace App\Livewire\chats;

use App\Jobs\SendTelegramMessage;
use App\Models\Chat;
use Exception;
use Illuminate\Support\Facades\App;
use Livewire\Component;
use Telegram\Bot\Exceptions\TelegramResponseException;
use Telegram\Bot\Laravel\Facades\Telegram;

use function PHPUnit\Framework\isEmpty;

class Message extends Component
{
    public $chat;
    public $message;
    public $done;
    public function mount($chat)
    {
        $this->chat = $chat;
    }

    public function render()
    {
        if(session('locale')!==null){
            App::setLocale(session('locale'));
         }
        return view('livewire.chats.message');
    }


    // public function send()
    // {
    //     $validated = $this->validate([
    //         'message' => 'required',
    //     ]);

    //         $response = Telegram::sendMessage([
    //             'chat_id' => $this->chat->id,
    //             'text' => $this->message,
    //         ]);
    //         if ($response) {

    //             session()->flash('success', trans('Message sent to user').': '.$this->chat->id);

    //         }else{
    //             session()->flash('danger', trans('Failed send to user ').$this->chat->id);
    //             return $this->redirect(route('chats.index'), navigate: true);
    //         }

    // }


    public function send()
{
    $validated = $this->validate([
        'message' => 'required',
    ]);

    try {
        SendTelegramMessage::dispatch($this->chat->id,$this->message,null,json_encode(['remove_keyboard' => true]));

        session()->flash('success', trans('Message sent to user') . ': ' . $this->chat->id);

    } catch (TelegramResponseException $e) {
        // تحقق من نص الخطأ
        if (str_contains($e->getMessage(), 'chat not found')) {
            session()->flash('danger', trans('Failed to send: chat not found for user ') . $this->chat->id);
        } else {
            session()->flash('danger', trans('Telegram API error: ') . $e->getMessage());
        }
        return $this->redirect(route('chats.index'), navigate: true);
    } catch (Exception $e) {
        session()->flash('danger', trans('Unexpected error: ') . $e->getMessage());
        return $this->redirect(route('chats.index'), navigate: true);
    }
}

}

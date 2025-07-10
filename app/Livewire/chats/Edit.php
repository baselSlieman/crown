<?php

namespace App\Livewire\chats;

use App\Jobs\SendTelegramMessage;
use Exception;
use Illuminate\Support\Facades\App;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Telegram\Bot\Exceptions\TelegramResponseException;
use Telegram\Bot\Laravel\Facades\Telegram;

class Edit extends Component
{
    #[Validate('required|numeric')]
    public $balance;

    public $info;

    public $chat;

    public function mount($chat)
    {
        $this->chat = $chat;
        $this->balance = $chat->balance;
        $this->info = $chat->info;
    }

    public function render()
    {
        if (session('locale') !== null) {
            App::setLocale(session('locale'));
        }
        return view('livewire.chats.edit');
    }

    public function save()
    {
        $this->validate();
        if ($this->balance > $this->chat->balance) {
            $diff = $this->balance - $this->chat->balance;
            try {
                SendTelegramMessage::dispatch($this->chat->id,'🎖 عزيزي ' . $this->chat->username . ':' . PHP_EOL .''. PHP_EOL .  ' تمت إضافة ' . $diff . 'NSP إلى رصيدك',null,json_encode(['remove_keyboard' => true]));
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
        } elseif ($this->balance < $this->chat->balance) {
            try{
                $diff = $this->chat->balance - $this->balance;
                SendTelegramMessage::dispatch($this->chat->id,'🎖 عزيزي ' . $this->chat->username . ':' . PHP_EOL .''. PHP_EOL .  ' تمّ خصم ' . $diff . 'NSP من رصيدك',null,json_encode(['remove_keyboard' => true]));
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
        $this->chat->update(
            $this->all()
        );
        session()->flash('success', trans('Success update chat') . ': ' . $this->chat->id);
        // return $this->redirect(route('chats.index'), navigate: true);
    }

    public function delete()
    {
        $this->chat->delete();
        session()->flash('success', trans('Success delete chat') . ': ' . $this->chat->id);
        return $this->redirect(route('chats.index'), navigate: true);
    }
}

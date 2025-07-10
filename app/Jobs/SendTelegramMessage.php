<?php

namespace App\Jobs;

use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Exceptions\TelegramResponseException;
class SendTelegramMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $chatId;
    protected $message;
    protected $parseMode;
    protected $replyMarkup;

    public function __construct($chatId, $message, $parseMode = null, $replyMarkup = null)
    {
        $this->chatId = $chatId;
        $this->message = $message;
        $this->parseMode = $parseMode;
        $this->replyMarkup = $replyMarkup;
    }

    public function handle()
    {
        $params = [
            'chat_id' => $this->chatId,
            'text' => $this->message,
        ];

        if ($this->parseMode) {
            $params['parse_mode'] = $this->parseMode;
        }

        if ($this->replyMarkup) {
            $params['reply_markup'] = $this->replyMarkup;
        }
        try {
            Telegram::sendMessage($params);
        }  catch (TelegramResponseException $e) {


        }catch (Exception $e) {
            // Log::error("broadcast-->chatid:".$this->chatId."-->".$e->getMessage());
        }
    }
}

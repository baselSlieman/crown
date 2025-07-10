<?php

namespace App\Http\Middleware;

use App\Jobs\SendTelegramMessage;
use App\Livewire\Setting;
use App\Models\Setting as ModelsSetting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBoot
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $value = $request->input('chat_id');
        $boton = ModelsSetting::first()->bootStatus;
        if ($boton) {
            return $next($request);
        }



        SendTelegramMessage::dispatch($value, "عذراً ..".PHP_EOL."".PHP_EOL.'🚧 البوت يخضع للصيانة في الوقت الحالي 🚧'.PHP_EOL."");
        return response()->json([],400);
    }
}

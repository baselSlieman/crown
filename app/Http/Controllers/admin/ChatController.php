<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\Chat;
use App\Models\Gift;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("admin.chat.index");
    }


    public function giftstore(Request $request){
        $chat_id = $request->chat_id;
        $amount = $request->amount;

        $hasOne= Gift::where('chat_id',$chat_id)->where('status', 'pending')->exists();
        if($hasOne){
            return redirect()->route('chats.usergifts',$chat_id)->with('danger','User has pending gift');
        }
        $chars = str_shuffle('abcdefghijklmnopqrstuvwxyz123456789');
        $code = substr($chars, 0, 5);
        $isCodeExists = Gift::where('code', $code)->exists();
        while($isCodeExists){
            $chars = str_shuffle('abcdefghijklmnopqrstuvwxyz123456789');
            $code = substr($chars, 0, 6);
            $isCodeExists = Gift::where('code', $code)->exists();
        }
        $created = Gift::Create([
            "chat_id"=>$chat_id,
            "amount"=>$amount,
            "code"=>$code
        ]);
        if($created){
            $response = Telegram::sendMessage([
                'chat_id' => $chat_id,
                'parse_mode'=> 'HTML',
                'text' => 'تهانينا 🎉'.PHP_EOL.''.PHP_EOL.'تم إهدائك قسيمة رصيد:'.PHP_EOL.'الكود:  <code>'.$code.'</code>'.PHP_EOL.'القيمة: '.$amount.' NSP'.PHP_EOL.''.PHP_EOL.'يمكن صرف القسيمة من واجهة 🎁 كود الهدايا'.PHP_EOL.''.PHP_EOL.'حظاً موفقاً...' ,
            ]);
            return redirect()->route('chats.usergifts',$chat_id)->with('success','Success send gift');
        }else{
            return redirect()->route('chats.usergifts',$chat_id)->with('danger','falied send gift');
        }

    }



    public function usergifts(Chat $chat){

        return view("admin.chat.gifts",compact("chat"));
    }


    public function userAffiliates(Chat $chat){
        return view("admin.chat.userAffiliates",compact("chat"));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function createMessage(Chat $chat)
    {
        return view("admin.chat.createMessage",compact('chat'));
    }
    public function sendMessage(Request $request,Chat $chat)
    {
        $response = Telegram::sendMessage([
            'chat_id' => $chat->id,
            'text' => $request->message,
        ]);
        return redirect()->route('chats.index')->with('success','Message sent');
    }
    /**
     * Update the specified resource in storage.
     */
    public function edit(Chat $chat)
    {
        return view("admin.chat.edit",compact('chat'));
    }

    public function update(Request $request, Chat $chat)
    {
        if($request->balance>$chat->balance){
            $diff = $request->balance-$chat->balance;
            $response = Telegram::sendMessage([
                    'chat_id' => $chat->id,
                    'text' => '🎖 عزيزي '.$chat->username.':'.PHP_EOL.' تمت إضافة '.$diff.' NSP إلى رصيدك' ,
            ]);
        }elseif($request->balance<$chat->balance){
            $diff = $chat->balance-$request->balance;
            $response = Telegram::sendMessage([
                'chat_id' => $chat->id,
                'text' => '🎖 عزيزي '.$chat->username.':'.PHP_EOL.' تمّ خضم '.$diff.' NSP من رصيدك' ,
        ]);
        }
        $chat->fill($request->all())->save();
        return redirect()->route('chats.index')->with('success','Chat updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Chat $chat)
    {
        $chat->delete();
        return redirect()->route('chats.index')->with('success','Chat deleted successfully');
    }
}

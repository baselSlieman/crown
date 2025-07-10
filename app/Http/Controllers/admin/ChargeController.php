<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Charge;
use App\Models\Chat;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class ChargeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $chat_id = $request->query('chat_id');
        return view("admin.charge.index",compact("chat_id"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function show(Charge $charge)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Charge $charge)
    {
        return view("admin.charge.edit",compact('charge'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Charge $charge)
    {
        $chat =Chat::find($charge->chat_id);
        if($request->amount>$charge->amount){
            $diff = $request->amount-$charge->amount;
            $chat->balance = $chat->balance+$diff;
            $updated = $chat->save();
            if($updated){
                $response = Telegram::sendMessage([
                    'chat_id' => $chat->id,
                    'text' => '🎖 عزيزي '.$chat->username.':'.PHP_EOL.' تمت إضافة '.$diff.' NSP إلى رصيدك' ,
                ]);
            }
        }elseif($request->amount<$charge->amount){
            $diff = $charge->amount-$request->amount;
            $chat->balance = $chat->balance-$diff;
            $chat->save();
        }
        $saved = $charge->fill($request->all())->save();
        if($saved){
            return redirect()->route('charges.index')->with('success','Charge updated');

        }else{
            return redirect()->route('charges.index')->with('danger','Failed to update charge');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Charge $charge)
    {
        $charge->delete();
        return redirect()->route('charges.index')->with('success','Charge deleted successfully');
    }
}
